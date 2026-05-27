<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Route;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceMiddlewareTimingTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.http.middleware_enabled', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    protected function defineRoutes($app): void
    {
        Route::middleware('obeserva.timing:api')->get('/timed', fn (): ResponseFactory|\Illuminate\Http\Response => response('ok'));
    }

    public function test_middleware_timing_alias_is_registered(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $router = $app->make('router');
        $this->assertArrayHasKey('obeserva.timing', $router->getMiddleware());
    }

    public function test_timing_middleware_records_child_span(): void
    {
        $response = $this->get('/timed');
        $response->assertOk();

        $app = $this->app;
        $this->assertNotNull($app);

        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $names = array_map(fn (Span $span): string => $span->getName(), $tracer->completedSpans());
        $this->assertContains('middleware.api', $names);
    }
}
