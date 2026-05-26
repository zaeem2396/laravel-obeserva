<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Laravel\Database\NPlusOneDetector;
use Obeserva\Laravel\Database\QueryCounter;
use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;

final readonly class FlushTracerOnTerminate
{
    public function __construct(
        private TracerInterface $tracer,
        private QueryCounter $queryCounter,
        private NPlusOneDetector $nPlusOneDetector,
        private ActiveJobSpanRegistry $jobSpanRegistry,
        private ActiveHorizonSupervisorRegistry $horizonSupervisorRegistry,
        private HorizonThroughputMetrics $horizonThroughputMetrics,
    ) {}

    public function handle(): void
    {
        $this->tracer->flush();
        $this->queryCounter->reset();
        $this->nPlusOneDetector->reset();
        $this->jobSpanRegistry->clear();
        $this->horizonSupervisorRegistry->clear();
        $this->horizonThroughputMetrics->reset();
    }
}
