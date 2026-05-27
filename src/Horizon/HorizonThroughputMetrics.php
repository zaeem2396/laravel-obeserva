<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

final class HorizonThroughputMetrics
{
    private int $jobsReserved = 0;

    private int $jobsReleased = 0;

    private int $workerRestarts = 0;

    private int $supervisorRestarts = 0;

    public function recordJobReserved(): void
    {
        $this->jobsReserved++;
    }

    public function recordJobReleased(): void
    {
        $this->jobsReleased++;
    }

    public function recordWorkerRestart(): void
    {
        $this->workerRestarts++;
    }

    public function recordSupervisorRestart(): void
    {
        $this->supervisorRestarts++;
    }

    /**
     * @return array<string, int>
     */
    public function toAttributes(): array
    {
        return [
            'horizon.jobs_reserved' => $this->jobsReserved,
            'horizon.jobs_released' => $this->jobsReleased,
            'horizon.worker_restarts' => $this->workerRestarts,
            'horizon.supervisor_restarts' => $this->supervisorRestarts,
        ];
    }

    public function reset(): void
    {
        $this->jobsReserved = 0;
        $this->jobsReleased = 0;
        $this->workerRestarts = 0;
        $this->supervisorRestarts = 0;
    }
}
