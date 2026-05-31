<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;

final readonly class ScoutDriverFactory
{
    public function __construct(
        private Application $app,
    ) {}

    public function makeLifecycleExporter(): SpanLifecycleExporterInterface
    {
        if (config('obeserva.driver') !== 'scout') {
            return new NoopSpanLifecycleExporter;
        }

        /** @var array<string, mixed> $scoutConfig */
        $scoutConfig = is_array(config('obeserva.scout')) ? config('obeserva.scout') : [];

        $config = ScoutConfig::fromArray($scoutConfig);

        if (! $config->enabled) {
            return new NoopSpanLifecycleExporter;
        }

        $client = $this->resolveClient();

        return new ScoutSpanExporter(
            client: $client,
            mapper: new ScoutSpanMapper,
            contextBridge: new ScoutContextBridge(
                client: $client,
                config: $config,
            ),
            config: $config,
        );
    }

    private function resolveClient(): ScoutApmClientInterface
    {
        if ($this->app->bound(ScoutApmClientInterface::class)) {
            return $this->app->make(ScoutApmClientInterface::class);
        }

        return new RecordingScoutApmClient(
            enabled: (bool) config('obeserva.scout.monitoring_enabled', false),
        );
    }
}
