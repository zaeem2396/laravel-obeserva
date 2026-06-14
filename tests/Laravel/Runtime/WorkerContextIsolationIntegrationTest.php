<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\IsolateWorkerContextAfterJobListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Runtime\WorkerContextResetter;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;
use Orchestra\Testbench\TestCase;

final class WorkerContextIsolationIntegrationTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalServer = [];

    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;

        parent::tearDown();
    }

    public function test_queue_worker_flushes_tracer_after_job_processed_event(): void
    {
        $_SERVER['argv'] = ['artisan', 'queue:work', 'redis'];
        config([
            'obeserva.worker.context_isolation' => true,
            'obeserva.worker.flush_after_job' => true,
            'obeserva.terminate.flush_tracer' => false,
        ]);

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('queue.process:ExampleJob', SpanKind::Consumer);
        $span->end();

        $this->assertSame(1, count($tracer->completedSpans()));

        Event::dispatch(new JobProcessed('redis', new FakeQueueJob));

        $this->assertSame(0, count($tracer->completedSpans()));
    }

    public function test_listener_can_be_invoked_directly(): void
    {
        $_SERVER['argv'] = ['artisan', 'horizon:work', 'redis'];

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('queue.process:ExampleJob', SpanKind::Consumer);
        $span->end();

        $listener = new IsolateWorkerContextAfterJobListener(
            new WorkerRuntimeDetector,
            $this->app->make(WorkerContextResetter::class),
        );

        $listener->handleJobProcessed(new JobProcessed('redis', new FakeQueueJob));

        $this->assertSame(0, count($tracer->completedSpans()));
    }
}

final class FakeQueueJob
{
    public function resolveName(): string
    {
        return self::class;
    }
}
