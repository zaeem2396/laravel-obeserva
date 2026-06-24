<?php

declare(strict_types=1);

namespace Obeserva\Core\Propagation;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;

final class TraceCarrierBag
{
    public const string CARRIER_KEY = 'obeserva';

    /**
     * @param  array<string, mixed>  $carrier
     * @return array<string, mixed>
     */
    public static function inject(
        TraceContextInterface $context,
        array $carrier,
        ?string $correlationId = null,
    ): array {
        $bag = array_merge(
            $context->toPropagationHeaders(),
            [
                'trace_id' => $context->getTraceId(),
                'parent_span_id' => $context->getSpanId(),
                'root_trace_id' => $context->getTraceId(),
            ],
        );

        if ($correlationId !== null && $correlationId !== '') {
            $bag['correlation_id'] = $correlationId;
        }

        $carrier[self::CARRIER_KEY] = $bag;

        return $carrier;
    }

    /**
     * @param  array<string, mixed>  $carrier
     */
    public static function extract(array $carrier): ?TraceContextInterface
    {
        $bag = $carrier[self::CARRIER_KEY] ?? null;

        if (! is_array($bag)) {
            return null;
        }

        /** @var array<string, mixed> $bag */
        $fromHeaders = TraceContext::fromPropagationHeaders($bag);

        if ($fromHeaders instanceof TraceContextInterface) {
            return new TraceContext(
                traceId: $fromHeaders->getTraceId(),
                spanId: $fromHeaders->getSpanId(),
                parentSpanId: self::stringOrNull($bag['parent_span_id'] ?? null),
                sampled: $fromHeaders->isSampled(),
            );
        }

        $traceId = self::stringOrNull($bag['trace_id'] ?? null);

        if ($traceId === null) {
            return null;
        }

        $parentSpanId = self::stringOrNull($bag['parent_span_id'] ?? null);

        return new TraceContext(
            traceId: $traceId,
            spanId: $parentSpanId ?? '',
            parentSpanId: $parentSpanId,
        );
    }

    /**
     * @param  array<string, mixed>  $carrier
     */
    public static function extractCorrelationId(array $carrier): ?string
    {
        $bag = $carrier[self::CARRIER_KEY] ?? null;

        if (! is_array($bag)) {
            return null;
        }

        return self::stringOrNull($bag['correlation_id'] ?? null);
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
