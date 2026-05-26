<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Route;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceRequestMiddlewareTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.http.middleware_enabled', true);
    }

    protected function defineRoutes($app): void
    {
        Route::get('/traced', fn (): ResponseFactory|\Illuminate\Http\Response => response('ok', 200))->name('traced');
    }

    public function test_middleware_records_http_span(): void
    {
        $response = $this->get('/traced');

        $response->assertOk();

        $app = $this->app;
        $this->assertNotNull($app);

        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);
        $completed = $tracer->completedSpans();

        $this->assertCount(1, $completed);
        $this->assertSame('traced', $completed[0]->getName());

        $attributes = $completed[0]->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame('GET', $attributes['http.method']);
        $this->assertSame(200, $attributes['http.status_code']);
        $this->assertArrayHasKey('http.duration_ms', $attributes);
    }
}
