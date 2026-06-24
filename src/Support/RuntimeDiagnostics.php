<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Support;

final readonly class RuntimeDiagnostics
{
    /**
     * @param  array<string, bool>  $features
     * @param  array<string, int|float>  $memory
     * @param  array<string, bool>  $flush
     */
    public function __construct(
        public string $packageVersion,
        public string $driver,
        public bool $enabled,
        public string $workerRuntime,
        public float $sampleRate,
        public array $features,
        public array $memory,
        public array $flush,
        public string $phpVersion,
        public string $laravelVersion,
        public string $appEnv,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'package_version' => $this->packageVersion,
            'driver' => $this->driver,
            'enabled' => $this->enabled,
            'worker_runtime' => $this->workerRuntime,
            'sample_rate' => $this->sampleRate,
            'features' => $this->features,
            'memory' => $this->memory,
            'flush' => $this->flush,
            'runtime' => [
                'php_version' => $this->phpVersion,
                'laravel_version' => $this->laravelVersion,
                'app_env' => $this->appEnv,
            ],
        ];
    }
}
