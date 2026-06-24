<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Support;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Laravel\Runtime\WorkerRuntimeDetector;

final readonly class RuntimeDiagnosticsBuilder
{
    public function __construct(
        private Application $app,
        private WorkerRuntimeDetector $runtimeDetector,
    ) {}

    public function build(): RuntimeDiagnostics
    {
        $laravelVersion = $this->app->version();

        $driver = config('obeserva.driver', 'noop');

        return new RuntimeDiagnostics(
            packageVersion: PackageVersion::version(),
            driver: is_string($driver) ? $driver : 'noop',
            enabled: (bool) config('obeserva.enabled', true),
            workerRuntime: $this->runtimeDetector->current()->value,
            sampleRate: $this->sampleRate(),
            features: $this->features(),
            memory: $this->memory(),
            flush: $this->flush(),
            phpVersion: PHP_VERSION,
            laravelVersion: $laravelVersion,
            appEnv: $this->app->environment(),
        );
    }

    private function sampleRate(): float
    {
        $probability = config('obeserva.sampling.probability', 1.0);

        return is_numeric($probability) ? (float) $probability : 1.0;
    }

    /**
     * @return array<string, bool>
     */
    private function features(): array
    {
        return [
            'http' => (bool) config('obeserva.http.middleware_enabled', true),
            'database' => (bool) config('obeserva.database.query_tracing', true),
            'queue' => (bool) config('obeserva.queue.job_tracing', true),
            'horizon' => (bool) config('obeserva.horizon.enabled', true),
            'cache' => (bool) config('obeserva.cache.enabled', true),
            'redis' => (bool) config('obeserva.redis.command_tracing', true),
            'events' => (bool) config('obeserva.events.tracing_enabled', true),
            'correlation' => (bool) config('obeserva.correlation.enabled', true),
            'summaries' => (bool) config('obeserva.summaries.enabled', true),
            'causation' => (bool) config('obeserva.causation.enabled', true),
            'telescope' => (bool) config('obeserva.development.telescope.enabled', false),
            'debug_toolbar' => (bool) config('obeserva.development.debug_toolbar.enabled', false),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function memory(): array
    {
        return [
            'max_completed_spans' => $this->configInt('obeserva.memory.max_completed_spans', 2048),
            'max_active_span_depth' => $this->configInt('obeserva.memory.max_active_span_depth', 256),
            'max_trace_snapshots' => $this->configInt('obeserva.memory.max_trace_snapshots', 512),
            'pressure_threshold_bytes' => $this->configInt('obeserva.memory.pressure_threshold_bytes', 0),
        ];
    }

    /**
     * @return array<string, bool>
     */
    private function flush(): array
    {
        return [
            'enabled' => (bool) config('obeserva.flush.enabled', true),
            'guard_exceptions' => (bool) config('obeserva.flush.guard_exceptions', true),
            'on_shutdown' => (bool) config('obeserva.flush.on_shutdown', true),
            'on_worker_stopping' => (bool) config('obeserva.flush.on_worker_stopping', true),
            'on_terminate' => (bool) config('obeserva.terminate.flush_tracer', true),
        ];
    }

    private function configInt(string $key, int $default): int
    {
        $value = config($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }
}
