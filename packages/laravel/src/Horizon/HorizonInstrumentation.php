<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Facades\Event;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonJobReleasedListener;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonJobReservedListener;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonSupervisorLoopedListener;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonSupervisorProcessRestartingListener;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonWorkerProcessRestartingListener;
use Obeserva\Laravel\Listeners\Horizon\TraceHorizonWorkerStoppingListener;

final class HorizonInstrumentation
{
    public static function register(bool $workerTracing, bool $throughputMetrics): void
    {
        if (! Horizon::isAvailable()) {
            return;
        }

        if ($workerTracing) {
            Event::listen(Horizon::SUPERVISOR_LOOPED, TraceHorizonSupervisorLoopedListener::class);
            Event::listen(Horizon::WORKER_PROCESS_RESTARTING, TraceHorizonWorkerProcessRestartingListener::class);
            Event::listen(Horizon::SUPERVISOR_PROCESS_RESTARTING, TraceHorizonSupervisorProcessRestartingListener::class);
            Event::listen(WorkerStopping::class, TraceHorizonWorkerStoppingListener::class);
        }

        if ($throughputMetrics) {
            Event::listen(Horizon::JOB_RESERVED, TraceHorizonJobReservedListener::class);
            Event::listen(Horizon::JOB_RELEASED, TraceHorizonJobReleasedListener::class);
        }
    }
}
