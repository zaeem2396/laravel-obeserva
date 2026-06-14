<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Obeserva\Laravel\Runtime\WorkerRuntime;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;
use Orchestra\Testbench\TestCase;

final class WorkerRuntimeDetectorTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalServer = [];

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

    public function test_detects_http_runtime_by_default(): void
    {
        unset($_SERVER['LARAVEL_OCTANE'], $_SERVER['RR_MODE'], $_SERVER['argv']);

        $this->assertSame(WorkerRuntime::Http, (new WorkerRuntimeDetector)->current());
    }

    public function test_detects_octane_runtime(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = '1';

        $this->assertSame(WorkerRuntime::Octane, (new WorkerRuntimeDetector)->current());
    }

    public function test_detects_roadrunner_runtime(): void
    {
        $_SERVER['RR_MODE'] = 'http';

        $this->assertSame(WorkerRuntime::RoadRunner, (new WorkerRuntimeDetector)->current());
    }

    public function test_detects_dedicated_queue_worker(): void
    {
        $_SERVER['argv'] = ['artisan', 'queue:work', 'redis'];

        $this->assertSame(WorkerRuntime::QueueWorker, (new WorkerRuntimeDetector)->current());
        $this->assertTrue((new WorkerRuntimeDetector)->shouldIsolateAfterJob());
    }

    public function test_does_not_isolate_after_job_in_http_runtime(): void
    {
        unset($_SERVER['argv']);

        config(['obeserva.worker.context_isolation' => true]);

        $this->assertFalse((new WorkerRuntimeDetector)->shouldIsolateAfterJob());
    }
}
