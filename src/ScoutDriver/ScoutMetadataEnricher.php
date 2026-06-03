<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Span\SpanInterface;

final readonly class ScoutMetadataEnricher
{
    public function __construct(
        private ScoutConfig $config,
        private ScoutSpanMetadataMapper $mapper,
        private ScoutRuntimeDiagnostics $diagnostics,
    ) {}

    /**
     * @return array<string, string>
     */
    public function runtimeTags(): array
    {
        if (! $this->config->metadataEnabled) {
            return [];
        }

        $tags = $this->diagnostics->toTags();

        if ($this->config->deploymentVersion !== '') {
            $tags['scout.deployment.version'] = $this->config->deploymentVersion;
        }

        if ($this->config->tenantId !== '') {
            $tags['scout.tenant.id'] = $this->config->tenantId;
        }

        return $tags;
    }

    /**
     * @return array<string, string>
     */
    public function spanTags(SpanInterface $span): array
    {
        if (! $this->config->metadataEnabled) {
            return [];
        }

        return $this->mapper->map($span);
    }
}
