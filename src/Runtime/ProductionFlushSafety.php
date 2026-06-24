<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Facades\Event;
use Obeserva\Laravel\Listeners\FlushTracerOnWorkerStoppingListener;

final class ProductionFlushSafety
{
    public static function register(Application $app): void
    {
        if (! (bool) config('obeserva.flush.enabled', true)) {
            return;
        }

        $app->make(ShutdownFlushRegistrar::class)->register($app);

        Event::listen(WorkerStopping::class, FlushTracerOnWorkerStoppingListener::class);
    }
}
