<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Obeserva\Contracts\Span\SpanInterface;

final readonly class OtelSpanConverter
{
    public function __construct(
        private OtelConfig $config,
        private OtelSpanKindMapper $kindMapper,
        private OtelSpanNameNormalizer $nameNormalizer,
        private OtelSemanticConventionMapper $semanticMapper,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function convert(SpanInterface $span): array
    {
        $attributes = $span->getAttributes();

        if ($this->config->semanticConventions) {
            $attributes = $this->semanticMapper->map($attributes);
        } else {
            $attributes = $this->filterScalarAttributes($attributes);
        }

        $resource = [
            'service.name' => $this->config->serviceName !== '' ? $this->config->serviceName : 'laravel',
        ];

        if ($this->config->serviceVersion !== '') {
            $resource['service.version'] = $this->config->serviceVersion;
        }

        return [
            'trace_id' => $span->getTraceId(),
            'span_id' => $span->getSpanId(),
            'parent_span_id' => $span->getParentSpanId(),
            'name' => $this->nameNormalizer->normalize($span),
            'kind' => $this->kindMapper->map($span->getKind()),
            'start_time_unix_nano' => $this->toUnixNano($span->getStartedAt()),
            'end_time_unix_nano' => $span->getEndedAt() !== null ? $this->toUnixNano($span->getEndedAt()) : null,
            'attributes' => $attributes,
            'resource' => $resource,
        ];
    }

    private function toUnixNano(float $timestamp): string
    {
        return (string) (int) round($timestamp * 1_000_000_000);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, string>
     */
    private function filterScalarAttributes(array $attributes): array
    {
        $filtered = [];

        foreach ($attributes as $key => $value) {
            if (! is_scalar($value) || $value === '') {
                continue;
            }

            $filtered[(string) $key] = (string) $value;
        }

        return $filtered;
    }
}
