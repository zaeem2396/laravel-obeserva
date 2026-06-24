<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Queue\Events\WorkerStopping;
use Obeserva\Laravel\Runtime\WorkerContextResetter;

final readonly class FlushTracerOnWorkerStoppingListener
{
    public function __construct(
        private WorkerContextResetter $contextResetter,
    ) {}

    public function handle(WorkerStopping $event): void
    {
        unset($event);

        if (! (bool) config('obeserva.flush.on_worker_stopping', true)) {
            return;
        }

        $this->contextResetter->reset();
    }
}
