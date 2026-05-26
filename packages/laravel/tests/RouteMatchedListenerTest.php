<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Http\RequestSpanEnricher;
use Obeserva\Laravel\Listeners\RouteMatchedListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class RouteMatchedListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_listener_enriches_active_request_span(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $context = $app->make(ContextManager::class);
        $span = new Span('request', SpanKind::Server, 'trace', 'span');
        $context->push($span);

        $route = new Route('GET', '/posts', fn (): string => 'ok');
        $route->name('posts.index');

        $listener = new RouteMatchedListener($context, new RequestSpanEnricher);
        $listener->handle(new RouteMatched($route, $app->make('request')));

        $attributes = $span->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame('posts.index', $attributes['laravel.route.name']);
        $this->assertNotEmpty($span->toArray()['events']);
    }
}
