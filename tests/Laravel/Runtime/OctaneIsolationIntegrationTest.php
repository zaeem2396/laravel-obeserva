<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\IsolateLongRunningWorkerContextListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Runtime\WorkerContextResetter;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;
use Orchestra\Testbench\TestCase;

final class OctaneIsolationIntegrationTest extends TestCase
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

    public function test_octane_runtime_resets_tracer_on_request_termination(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = 1;
        config([
            'obeserva.worker.context_isolation' => true,
            'obeserva.worker.octane_isolation' => true,
            'obeserva.terminate.flush_tracer' => false,
        ]);

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('http.request', SpanKind::Server);
        $span->end();

        $this->assertSame(1, count($tracer->completedSpans()));

        $listener = new IsolateLongRunningWorkerContextListener(
            new WorkerRuntimeDetector,
            $this->app->make(WorkerContextResetter::class),
        );

        $listener->handle();

        $this->assertSame(0, count($tracer->completedSpans()));
    }
}
