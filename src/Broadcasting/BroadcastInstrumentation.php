<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Broadcasting;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Obeserva\Laravel\Listeners\Broadcasting\TraceBroadcastListener;

final class BroadcastInstrumentation
{
    public static function register(Application $app, bool $enabled): void
    {
        if (! $enabled || ! class_exists(BroadcastEvent::class)) {
            return;
        }

        Event::listen(BroadcastEvent::class, TraceBroadcastListener::class);
    }
}
