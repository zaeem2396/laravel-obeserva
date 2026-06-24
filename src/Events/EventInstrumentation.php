<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Propagation\PropagationContextResolver;

final class EventInstrumentation
{
    public static function register(
        Application $app,
        bool $propagationEnabled,
        bool $tracingEnabled,
    ): void {
        if (! $propagationEnabled && ! $tracingEnabled) {
            return;
        }

        $app->booted(function () use ($app, $propagationEnabled, $tracingEnabled): void {
            $dispatcher = Event::getFacadeRoot();

            if (! $dispatcher instanceof Dispatcher || $dispatcher instanceof TracePropagatingEventDispatcher) {
                return;
            }

            Event::swap(new TracePropagatingEventDispatcher(
                $dispatcher,
                $app->make(PropagationContextResolver::class),
                $app->make(ContextStorageInterface::class),
                $app->make(CorrelationContextStorage::class),
                fn (): TracerInterface => $app->make(TracerInterface::class),
                $propagationEnabled,
                $tracingEnabled,
            ));
        });
    }
}
