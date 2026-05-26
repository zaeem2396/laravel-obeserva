<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Horizon;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonSupervisorLoopedListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceHorizonSupervisorLoopedListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_supervisor_looped_creates_internal_span_with_metrics(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $listener = new TraceHorizonSupervisorLoopedListener(
            $app->make(TracerInterface::class),
            $app->make(ActiveHorizonSupervisorRegistry::class),
            $app->make(HorizonThroughputMetrics::class),
        );

        $metrics = $app->make(HorizonThroughputMetrics::class);
        $metrics->recordJobReserved();

        $supervisor = new \stdClass;
        $supervisor->name = 'supervisor-1';

        $event = new \stdClass;
        $event->supervisor = $supervisor;

        $listener->handle($event);

        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $names = array_map(
            static fn (Span $span): string => $span->getName(),
            $tracer->completedSpans(),
        );

        $this->assertNotContains('horizon.supervisor:supervisor-1', $names);

        $active = $app->make(ActiveHorizonSupervisorRegistry::class)->get('supervisor-1');
        $this->assertInstanceOf(Span::class, $active);
        $this->assertSame('horizon.supervisor:supervisor-1', $active->getName());
        /** @var array<string, mixed> $attributes */
        $attributes = $active->toArray()['attributes'];
        $this->assertSame(1, $attributes['horizon.jobs_reserved'] ?? null);
    }
}
