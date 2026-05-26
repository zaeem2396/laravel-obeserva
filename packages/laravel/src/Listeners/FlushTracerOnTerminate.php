<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Laravel\Database\NPlusOneDetector;
use Obeserva\Laravel\Database\QueryCounter;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;

final readonly class FlushTracerOnTerminate
{
    public function __construct(
        private TracerInterface $tracer,
        private QueryCounter $queryCounter,
        private NPlusOneDetector $nPlusOneDetector,
        private ActiveJobSpanRegistry $jobSpanRegistry,
    ) {}

    public function handle(): void
    {
        $this->tracer->flush();
        $this->queryCounter->reset();
        $this->nPlusOneDetector->reset();
        $this->jobSpanRegistry->clear();
    }
}
