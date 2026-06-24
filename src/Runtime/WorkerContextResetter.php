<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Flush\TracerFlushGuard;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryRegistry;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Database\NPlusOneDetector;
use Obeserva\Laravel\Database\QueryCounter;
use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;

final readonly class WorkerContextResetter
{
    public function __construct(
        private TracerFlushGuard $flushGuard,
        private ContextManager $contextManager,
        private QueryCounter $queryCounter,
        private NPlusOneDetector $nPlusOneDetector,
        private ActiveJobSpanRegistry $jobSpanRegistry,
        private ActiveHorizonSupervisorRegistry $horizonSupervisorRegistry,
        private HorizonThroughputMetrics $horizonThroughputMetrics,
        private CorrelationContextStorage $correlationStorage,
        private TraceSummaryRegistry $summaryRegistry,
    ) {}

    public function reset(): void
    {
        $this->flushGuard->flush();
        $this->contextManager->clear();
        $this->queryCounter->reset();
        $this->nPlusOneDetector->reset();
        $this->jobSpanRegistry->clear();
        $this->horizonSupervisorRegistry->clear();
        $this->horizonThroughputMetrics->reset();
        $this->correlationStorage->clear();
        $this->summaryRegistry->clear();
    }
}
