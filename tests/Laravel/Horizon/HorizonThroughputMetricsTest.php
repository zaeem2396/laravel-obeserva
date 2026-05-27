<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Horizon;

use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;
use PHPUnit\Framework\TestCase;

final class HorizonThroughputMetricsTest extends TestCase
{
    public function test_records_throughput_counters(): void
    {
        $metrics = new HorizonThroughputMetrics;

        $metrics->recordJobReserved();
        $metrics->recordJobReserved();
        $metrics->recordJobReleased();
        $metrics->recordWorkerRestart();
        $metrics->recordSupervisorRestart();

        $this->assertSame([
            'horizon.jobs_reserved' => 2,
            'horizon.jobs_released' => 1,
            'horizon.worker_restarts' => 1,
            'horizon.supervisor_restarts' => 1,
        ], $metrics->toAttributes());

        $metrics->reset();

        $this->assertSame([
            'horizon.jobs_reserved' => 0,
            'horizon.jobs_released' => 0,
            'horizon.worker_restarts' => 0,
            'horizon.supervisor_restarts' => 0,
        ], $metrics->toAttributes());
    }
}
