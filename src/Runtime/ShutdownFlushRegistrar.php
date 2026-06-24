<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Laravel\Listeners\FlushTracerOnTerminate;

final class ShutdownFlushRegistrar
{
    private static bool $registered = false;

    public function register(Application $app): void
    {
        if (self::$registered || ! (bool) config('obeserva.flush.on_shutdown', true)) {
            return;
        }

        self::$registered = true;

        register_shutdown_function(static function () use ($app): void {
            if (! $app->bound(FlushTracerOnTerminate::class)) {
                return;
            }

            $app->make(FlushTracerOnTerminate::class)->handle();
        });
    }
}
