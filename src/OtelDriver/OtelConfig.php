<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

final readonly class OtelConfig
{
    public function __construct(
        public bool $enabled,
        public string $serviceName,
        public string $serviceVersion,
        public bool $semanticConventions,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            enabled: (bool) ($config['enabled'] ?? true),
            serviceName: is_string($config['service_name'] ?? null) ? $config['service_name'] : '',
            serviceVersion: is_string($config['service_version'] ?? null) ? $config['service_version'] : '',
            semanticConventions: (bool) ($config['semantic_conventions'] ?? true),
        );
    }
}
