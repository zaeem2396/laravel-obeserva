<?php

declare(strict_types=1);

namespace Obeserva\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
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
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/obeserva.php', 'obeserva');

        $this->app->singleton(ContextStorageInterface::class, ContextManager::class);
        $this->app->singleton(ContextManager::class);

        $this->app->singleton(SamplerInterface::class, function (): SamplerInterface {
            $probability = config('obeserva.sampling.probability', 1.0);
            $rate = is_numeric($probability) ? (float) $probability : 1.0;

            return $rate >= 1.0
                ? new AlwaysOnSampler
                : new ProbabilitySampler($rate);
        });

        $this->app->singleton(TracerInterface::class, function (Application $app): TracerInterface {
            return new Tracer($app->make(SamplerInterface::class));
        });
    }

    public function boot(): void
    {
        if (! config('obeserva.enabled', true)) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/obeserva.php' => config_path('obeserva.php'),
        ], 'obeserva-config');

        if (config('obeserva.http.middleware_enabled', true) && $this->app->bound(Kernel::class)) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->prependMiddleware(TraceRequestMiddleware::class);
        }
    }
}
