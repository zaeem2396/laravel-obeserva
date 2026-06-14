<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;

final readonly class OtelDriverFactory
{
    public function __construct(
        private Application $app,
    ) {}

    public function makeLifecycleExporter(): SpanLifecycleExporterInterface
    {
        if (config('obeserva.driver') !== 'otel') {
            return new NoopSpanLifecycleExporter;
        }

        /** @var array<string, mixed> $otelConfig */
        $otelConfig = is_array(config('obeserva.otel')) ? config('obeserva.otel') : [];

        $config = OtelConfig::fromArray($otelConfig);

        if (! $config->enabled) {
            return new NoopSpanLifecycleExporter;
        }

        $client = $this->resolveClient();

        return new OtelSpanExporter(
            client: $client,
            converter: new OtelSpanConverter(
                config: $config,
                kindMapper: new OtelSpanKindMapper,
                nameNormalizer: new OtelSpanNameNormalizer,
                semanticMapper: new OtelSemanticConventionMapper,
            ),
            config: $config,
        );
    }

    private function resolveClient(): OtelExporterClientInterface
    {
        if ($this->app->bound(OtelExporterClientInterface::class)) {
            return $this->app->make(OtelExporterClientInterface::class);
        }

        return new RecordingOtelExporterClient(enabled: true);
    }
}
