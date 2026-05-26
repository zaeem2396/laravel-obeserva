<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Queue;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Tests\Queue\Jobs\TestQueueJob;
use Orchestra\Testbench\TestCase;

final class QueueTracePropagationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.queue.propagation_enabled', true);
        $app['config']->set('obeserva.queue.job_tracing', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
        $app['config']->set('queue.default', 'sync');
    }

    public function test_sync_queue_job_creates_consumer_span(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $app->make(ContextManager::class);
        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $requestSpan = $tracer->startSpan('request', SpanKind::Server);
        $requestSpan->end();

        TestQueueJob::dispatch();

        $names = array_map(
            fn (Span $span): string => $span->getName(),
            $tracer->completedSpans(),
        );

        $this->assertContains('request', $names);
        $this->assertContains('queue.process:'.TestQueueJob::class, $names);
    }
}
