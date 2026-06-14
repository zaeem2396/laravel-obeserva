<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Obeserva\Laravel\Runtime\WorkerContextResetter;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;

final readonly class IsolateWorkerContextAfterJobListener
{
    public function __construct(
        private WorkerRuntimeDetector $runtimeDetector,
        private WorkerContextResetter $contextResetter,
    ) {}

    public function handleJobProcessed(JobProcessed $event): void
    {
        unset($event);
        $this->resetWhenIsolated();
    }

    public function handleJobFailed(JobFailed $event): void
    {
        unset($event);
        $this->resetWhenIsolated();
    }

    private function resetWhenIsolated(): void
    {
        if (! $this->runtimeDetector->shouldIsolateAfterJob()) {
            return;
        }

        if (! (bool) config('obeserva.worker.flush_after_job', true)) {
            return;
        }

        $this->contextResetter->reset();
    }
}
