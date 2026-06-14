<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Driver;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Core\Export\CompositeSpanLifecycleExporter;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;
use Obeserva\DeveloperExperience\SpanSnapshotCollector;
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

        $primary = match ($driver) {
            'scout' => $this->app->make(ScoutDriverFactory::class)->makeLifecycleExporter(),
            'otel' => $this->app->make(OtelDriverFactory::class)->makeLifecycleExporter(),
            default => new NoopSpanLifecycleExporter,
        };

        if (! $this->developmentCollectionEnabled()) {
            return $primary;
        }

        return new CompositeSpanLifecycleExporter([
            $primary,
            $this->app->make(SpanSnapshotCollector::class),
        ]);
    }

    private function developmentCollectionEnabled(): bool
    {
        return (bool) config('obeserva.development.telescope.enabled', false)
            || (bool) config('obeserva.development.debug_toolbar.enabled', false);
    }
}
