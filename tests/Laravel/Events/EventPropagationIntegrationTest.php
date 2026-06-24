<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Events;

use Illuminate\Support\Facades\Event;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Events\Concerns\InteractsWithTraceContext;
use Obeserva\Laravel\Events\EventTraceContextCarrier;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class UserRegisteredForTracing
{
    use InteractsWithTraceContext;

    public function __construct(public int $userId) {}
}

final class EventPropagationIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.events.propagation_enabled', true);
        $app['config']->set('obeserva.events.tracing_enabled', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_dispatched_events_carry_trace_context_and_spans(): void
    {
        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $producer = $tracer->startSpan('http.request');
        $producer->end();

        $received = null;

        Event::listen(UserRegisteredForTracing::class, function (UserRegisteredForTracing $event) use (&$received): void {
            $received = $event;
        });

        $event = new UserRegisteredForTracing(42);
        Event::dispatch($event);

        $this->assertNotNull($received);
        $this->assertNotNull(EventTraceContextCarrier::extract($received));

        $names = array_map(
            static fn (Span $span): string => $span->getName(),
            $tracer->completedSpans(),
        );

        $this->assertContains('http.request', $names);
        $this->assertContains('event.dispatch:UserRegisteredForTracing', $names);
    }
}
