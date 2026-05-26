<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;

final class TraceContextCarrier
{
    public const string PAYLOAD_KEY = 'obeserva';

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function inject(TraceContextInterface $context, array $payload): array
    {
        $payload[self::PAYLOAD_KEY] = array_merge(
            $context->toPropagationHeaders(),
            [
                'trace_id' => $context->getTraceId(),
                'parent_span_id' => $context->getSpanId(),
            ],
        );

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function extract(array $payload): ?TraceContextInterface
    {
        $carrier = $payload[self::PAYLOAD_KEY] ?? null;

        if (! is_array($carrier)) {
            return null;
        }

        /** @var array<string, mixed> $headers */
        $headers = $carrier;
        $fromHeaders = TraceContext::fromPropagationHeaders($headers);

        if ($fromHeaders instanceof TraceContextInterface) {
            return new TraceContext(
                traceId: $fromHeaders->getTraceId(),
                spanId: $fromHeaders->getSpanId(),
                parentSpanId: self::stringOrNull($carrier['parent_span_id'] ?? null),
                sampled: $fromHeaders->isSampled(),
            );
        }

        $traceId = self::stringOrNull($carrier['trace_id'] ?? null);

        if ($traceId === null) {
            return null;
        }

        return new TraceContext(
            traceId: $traceId,
            spanId: self::stringOrNull($carrier['span_id'] ?? '') ?? '',
            parentSpanId: self::stringOrNull($carrier['parent_span_id'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function decodeJobPayload(string $rawBody): array
    {
        $decoded = json_decode($rawBody, true);

        if (! is_array($decoded)) {
            return [];
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
