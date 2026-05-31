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
        );
    }
}
