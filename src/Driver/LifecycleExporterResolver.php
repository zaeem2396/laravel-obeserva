<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Driver;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;
use Obeserva\OtelDriver\OtelDriverFactory;
use Obeserva\ScoutDriver\ScoutDriverFactory;

final readonly class LifecycleExporterResolver
{
    public function __construct(
        private Application $app,
    ) {}

    public function resolve(): SpanLifecycleExporterInterface
    {
        $driver = config('obeserva.driver', 'noop');

        return match ($driver) {
            'scout' => $this->app->make(ScoutDriverFactory::class)->makeLifecycleExporter(),
            'otel' => $this->app->make(OtelDriverFactory::class)->makeLifecycleExporter(),
            default => new NoopSpanLifecycleExporter,
        };
    }
}
