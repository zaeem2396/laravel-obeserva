<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Laravel\Queue\TraceContextCarrier;

final class HorizonRetryCorrelator
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function enrichSpan(SpanInterface $span, array $payload): void
    {
        $carrier = $payload[TraceContextCarrier::PAYLOAD_KEY] ?? null;

        if (! is_array($carrier)) {
            return;
        }

        if (isset($carrier['root_trace_id']) && is_string($carrier['root_trace_id'])) {
            $span->setAttribute('trace.root_trace_id', $carrier['root_trace_id']);
        }

        if (isset($carrier['retry_attempt']) && is_numeric($carrier['retry_attempt'])) {
            $span->setAttribute('queue.retry_attempt', (int) $carrier['retry_attempt']);
        }
    }

    /**
     * @param  array<string, mixed>  $carrier
     * @return array<string, mixed>
     */
    public static function stampRetryAttempt(array $carrier, int $attempt): array
    {
        $carrier['retry_attempt'] = $attempt;

        if (! isset($carrier['root_trace_id']) && isset($carrier['trace_id']) && is_string($carrier['trace_id'])) {
            $carrier['root_trace_id'] = $carrier['trace_id'];
        }

        return $carrier;
    }
}
