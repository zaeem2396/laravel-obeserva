<?php

declare(strict_types=1);

namespace Obeserva\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\SamplerInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Sampling\ProbabilitySampler;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Http\TraceRequestMiddleware;

final class ObeservaServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/obeserva.php', 'obeserva');

        $this->app->singleton(ContextManager::class);
        $this->app->singleton(ContextStorageInterface::class, ContextManager::class);
        $this->app->singleton(ActiveSpanStorageInterface::class, ContextManager::class);

        $this->app->singleton(SamplerInterface::class, function (): SamplerInterface {
            $probability = config('obeserva.sampling.probability', 1.0);
            $rate = is_numeric($probability) ? (float) $probability : 1.0;

            return $rate >= 1.0
                ? new AlwaysOnSampler
                : new ProbabilitySampler($rate);
        });

        $this->app->singleton(TracerInterface::class, function (Application $app): TracerInterface {
            $context = $app->make(ContextManager::class);

            return new Tracer(
                $app->make(SamplerInterface::class),
                $context,
                $context,
            );
        });

        $this->app->alias(TracerInterface::class, Tracer::class);
    }

    public function boot(): void
    {
        if (! config('obeserva.enabled', true)) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/obeserva.php' => config_path('obeserva.php'),
        ], 'obeserva-config');

        if (! config('obeserva.http.middleware_enabled', true)) {
            return;
        }

        $this->app->booted(function (): void {
            if (! $this->app->bound(Kernel::class)) {
                return;
            }

            $this->app->make(Kernel::class)->prependMiddleware(TraceRequestMiddleware::class);
        });
    }
}
