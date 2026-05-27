<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Horizon;

use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;

final readonly class TraceHorizonWorkerStoppingListener
{
    public function __construct(
        private ActiveHorizonSupervisorRegistry $supervisorRegistry,
    ) {}

    public function handle(): void
    {
        foreach ($this->supervisorRegistry->all() as $span) {
            if (! $span->isEnded()) {
                $span->setAttribute('horizon.worker.status', 'stopping');
                $span->addEvent('horizon.supervisor.stopping');
                $span->end();
            }
        }
        $this->supervisorRegistry->clear();
    }
}
