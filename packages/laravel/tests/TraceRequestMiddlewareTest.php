<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Support\Facades\Route;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceRequestMiddlewareTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineRoutes($app): void
    {
        Route::get('/traced', fn () => response('ok', 200))->name('traced');
    }

    public function test_middleware_records_http_span(): void
    {
        config(['obeserva.enabled' => true, 'obeserva.http.middleware_enabled' => true]);

        $response = $this->get('/traced');

        $response->assertOk();

        $tracer = $this->app->make(Tracer::class);
        $completed = $tracer->completedSpans();

        $this->assertCount(1, $completed);
        $this->assertSame('traced', $completed[0]->getName());
        $this->assertSame('GET', $completed[0]->toArray()['attributes']['http.method']);
        $this->assertSame(200, $completed[0]->toArray()['attributes']['http.status_code']);
        $this->assertArrayHasKey('http.duration_ms', $completed[0]->toArray()['attributes']);
    }
}
