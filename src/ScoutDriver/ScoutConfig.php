<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

final readonly class ScoutConfig
{
    /**
     * @param  array<string, string>  $defaultTags
     */
    public function __construct(
        public bool $enabled,
        public string $applicationName,
        public string $key,
        public bool $monitoringEnabled,
        public array $defaultTags,
        public string $deploymentVersion = '',
        public string $tenantId = '',
        public bool $metadataEnabled = true,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config): self
    {
        /** @var array<string, string> $defaultTags */
        $defaultTags = is_array($config['default_tags'] ?? null) ? $config['default_tags'] : [];

        return new self(
            enabled: (bool) ($config['enabled'] ?? true),
            applicationName: is_string($config['application_name'] ?? null) ? $config['application_name'] : '',
            key: is_string($config['key'] ?? null) ? $config['key'] : '',
            monitoringEnabled: (bool) ($config['monitoring_enabled'] ?? false),
            defaultTags: $defaultTags,
            deploymentVersion: is_string($config['deployment_version'] ?? null) ? $config['deployment_version'] : '',
            tenantId: is_string($config['tenant_id'] ?? null) ? $config['tenant_id'] : '',
            metadataEnabled: (bool) ($config['metadata_enabled'] ?? true),
        );
    }
}
