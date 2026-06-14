<?php

declare(strict_types=1);

namespace Obeserva\Laravel;

use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Queue;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\SamplerInterface;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Sampling\ProbabilitySampler;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Database\NPlusOneDetector;
use Obeserva\Laravel\Database\QueryCounter;
use Obeserva\Laravel\Database\QuerySanitizer;
use Obeserva\Laravel\Driver\LifecycleExporterResolver;
use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;
use Obeserva\Laravel\Horizon\HorizonInstrumentation;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;
use Obeserva\Laravel\Http\Middleware\TraceMiddlewareTiming;
use Obeserva\Laravel\Http\RequestSpanEnricher;
use Obeserva\Laravel\Http\TraceRequestMiddleware;
use Obeserva\Laravel\Listeners\FlushTracerOnTerminate;
use Obeserva\Laravel\Listeners\NPlusOneDetectionListener;
use Obeserva\Laravel\Listeners\ReportExceptionListener;
use Obeserva\Laravel\Listeners\RouteMatchedListener;
use Obeserva\Laravel\Listeners\TraceCacheEventListener;
use Obeserva\Laravel\Listeners\TraceJobFailedListener;
use Obeserva\Laravel\Listeners\TraceJobProcessedListener;
use Obeserva\Laravel\Listeners\TraceJobProcessingListener;
use Obeserva\Laravel\Listeners\TraceQueryListener;
use Obeserva\Laravel\Listeners\TraceRedisCommandExecutedListener;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;
use Obeserva\Laravel\Queue\JobSpanEnricher;
use Obeserva\Laravel\Queue\QueuePayloadHook;
use Obeserva\OtelDriver\OtelDriverFactory;
use Obeserva\ScoutDriver\ContainerScoutApmClient;
use Obeserva\ScoutDriver\ScoutApmClientInterface;
use Obeserva\ScoutDriver\ScoutDriverFactory;

final class ObeservaServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/obeserva.php', 'obeserva');

        $this->app->singleton(RequestSpanEnricher::class);
        $this->app->singleton(QuerySanitizer::class);
        $this->app->singleton(QueryCounter::class);
        $this->app->singleton(NPlusOneDetector::class);
        $this->app->singleton(NPlusOneDetectionListener::class);
        $this->app->singleton(ActiveJobSpanRegistry::class);
        $this->app->singleton(JobSpanEnricher::class);
        $this->app->singleton(QueuePayloadHook::class);
        $this->app->singleton(ActiveHorizonSupervisorRegistry::class);
        $this->app->singleton(HorizonThroughputMetrics::class);
        $this->app->singleton(ContextManager::class);
        $this->app->singleton(ContextStorageInterface::class, ContextManager::class);
        $this->app->singleton(ActiveSpanStorageInterface::class, ContextManager::class);

        $this->registerDrivers();

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
                $app->make(SpanLifecycleExporterInterface::class),
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

        $this->registerHttpInstrumentation();
        $this->registerDatabaseInstrumentation();
        $this->registerQueueInstrumentation();
        $this->registerHorizonInstrumentation();
        $this->registerCacheInstrumentation();
        $this->registerRedisInstrumentation();
        $this->registerExceptionInstrumentation();
        $this->registerTerminateHook();
    }

    private function registerDrivers(): void
    {
        $this->app->singleton(ScoutDriverFactory::class);
        $this->app->singleton(OtelDriverFactory::class);
        $this->app->singleton(LifecycleExporterResolver::class);

        $this->app->singleton(
            SpanLifecycleExporterInterface::class,
            fn (Application $app): SpanLifecycleExporterInterface => $app->make(LifecycleExporterResolver::class)->resolve(),
        );

        $this->app->singleton(ScoutApmClientInterface::class, fn (Application $app): ScoutApmClientInterface => new ContainerScoutApmClient($app));
    }

    private function registerQueueInstrumentation(): void
    {
        if (config('obeserva.queue.propagation_enabled', true)) {
            Queue::createPayloadUsing($this->app->make(QueuePayloadHook::class));
        }

        if (! config('obeserva.queue.job_tracing', true)) {
            return;
        }

        Event::listen(JobProcessing::class, TraceJobProcessingListener::class);
        Event::listen(JobProcessed::class, TraceJobProcessedListener::class);

        if (config('obeserva.queue.failed_job_correlation', true)) {
            Event::listen(JobFailed::class, TraceJobFailedListener::class);
        }
    }

    private function registerHorizonInstrumentation(): void
    {
        if (! config('obeserva.horizon.enabled', true)) {
            return;
        }

        HorizonInstrumentation::register(
            workerTracing: (bool) config('obeserva.horizon.worker_tracing', true),
            throughputMetrics: (bool) config('obeserva.horizon.throughput_metrics', true),
        );
    }

    private function registerDatabaseInstrumentation(): void
    {
        if (config('obeserva.database.query_tracing', true)) {
            Event::listen(QueryExecuted::class, TraceQueryListener::class);
        }
    }

    private function registerCacheInstrumentation(): void
    {
        if (! config('obeserva.cache.enabled', true)) {
            return;
        }

        Event::listen(CacheHit::class, TraceCacheEventListener::class);
        Event::listen(CacheMissed::class, TraceCacheEventListener::class);
        Event::listen(KeyWritten::class, TraceCacheEventListener::class);
        Event::listen(KeyForgotten::class, TraceCacheEventListener::class);
    }

    private function registerRedisInstrumentation(): void
    {
        if (! config('obeserva.redis.command_tracing', true)) {
            return;
        }

        Event::listen(CommandExecuted::class, TraceRedisCommandExecutedListener::class);
    }

    private function registerHttpInstrumentation(): void
    {
        if (! config('obeserva.http.middleware_enabled', true)) {
            return;
        }

        if (config('obeserva.http.middleware_timing_alias', true) && $this->app->bound(Router::class)) {
            $this->app->make(Router::class)->aliasMiddleware(
                'obeserva.timing',
                TraceMiddlewareTiming::class,
            );
        }

        Event::listen(RouteMatched::class, RouteMatchedListener::class);

        $this->app->booted(function (): void {
            if (! $this->app->bound(Kernel::class)) {
                return;
            }

            $this->app->make(Kernel::class)->prependMiddleware(TraceRequestMiddleware::class);
        });
    }

    private function registerExceptionInstrumentation(): void
    {
        if (! config('obeserva.exceptions.enabled', true)) {
            return;
        }

        $this->callAfterResolving(ExceptionHandler::class, function (ExceptionHandler $handler): void {
            if (! $handler instanceof Handler) {
                return;
            }

            $listener = $this->app->make(ReportExceptionListener::class);

            $handler->reportable(function (\Throwable $exception) use ($listener): void {
                $listener->report($exception);
            });
        });
    }

    private function registerTerminateHook(): void
    {
        if (! config('obeserva.terminate.flush_tracer', true)) {
            return;
        }

        $this->app->terminating(function (): void {
            $this->app->make(FlushTracerOnTerminate::class)->handle();
        });
    }
}
