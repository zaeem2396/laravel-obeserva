<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests\Memory;

use Obeserva\Core\Memory\MemoryPressureMonitor;
use PHPUnit\Framework\TestCase;

final class MemoryPressureMonitorTest extends TestCase
{
    public function test_disabled_when_threshold_is_zero(): void
    {
        $monitor = new MemoryPressureMonitor(0);

        $this->assertFalse($monitor->isUnderPressure());
    }

    public function test_detects_pressure_when_threshold_exceeded(): void
    {
        $monitor = new MemoryPressureMonitor(1);

        $this->assertTrue($monitor->isUnderPressure());
    }
}
