<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Illuminate\Queue\Events\WorkerStopping;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\FlushTracerOnWorkerStoppingListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class FlushTracerOnWorkerStoppingListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.flush.on_worker_stopping', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_flushes_tracer_when_worker_stops(): void
    {
        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('worker.job', SpanKind::Consumer);
        $span->end();

        $this->assertSame(1, count($tracer->completedSpans()));

        $this->app->make(FlushTracerOnWorkerStoppingListener::class)
            ->handle(new WorkerStopping('redis', 0));

        $this->assertSame(0, count($tracer->completedSpans()));
    }
}
