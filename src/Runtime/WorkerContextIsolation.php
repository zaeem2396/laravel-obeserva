<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Obeserva\Laravel\Listeners\IsolateLongRunningWorkerContextListener;
use Obeserva\Laravel\Listeners\IsolateWorkerContextAfterJobListener;

final class WorkerContextIsolation
{
    public static function register(): void
    {
        if (! (bool) config('obeserva.worker.context_isolation', true)) {
            return;
        }

        Event::listen(JobProcessed::class, [IsolateWorkerContextAfterJobListener::class, 'handleJobProcessed']);
        Event::listen(JobFailed::class, [IsolateWorkerContextAfterJobListener::class, 'handleJobFailed']);

        self::registerOctaneListeners();
        self::registerRoadRunnerListeners();
    }

    private static function registerOctaneListeners(): void
    {
        if (! (bool) config('obeserva.worker.octane_isolation', true)) {
            return;
        }

        $events = [
            'Laravel\Octane\Events\RequestTerminated',
            'Laravel\Octane\Events\TaskTerminated',
            'Laravel\Octane\Events\TickTerminated',
        ];

        foreach ($events as $event) {
            if (class_exists($event)) {
                Event::listen($event, IsolateLongRunningWorkerContextListener::class);
            }
        }
    }

    private static function registerRoadRunnerListeners(): void
    {
        if (! (bool) config('obeserva.worker.roadrunner_isolation', true)) {
            return;
        }

        $events = [
            'Spiral\RoadRunnerLaravel\Events\WorkerStopping',
            'Spiral\RoadRunner\Http\Event\RequestTerminated',
        ];

        foreach ($events as $event) {
            if (class_exists($event)) {
                Event::listen($event, IsolateLongRunningWorkerContextListener::class);
            }
        }
    }
}
