<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Laravel\Runtime\WorkerContextResetter;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;

final readonly class IsolateLongRunningWorkerContextListener
{
    public function __construct(
        private WorkerRuntimeDetector $runtimeDetector,
        private WorkerContextResetter $contextResetter,
    ) {}

    public function handle(): void
    {
        if (! $this->runtimeDetector->shouldIsolateOnLongRunningRequestEnd()) {
            return;
        }

        $this->contextResetter->reset();
    }
}
