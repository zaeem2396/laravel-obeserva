<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Illuminate\Contracts\Queue\Job;
use Obeserva\Contracts\Span\SpanInterface;

final class JobSpanEnricher
{
    public function enrich(SpanInterface $span, Job $job, string $connectionName): void
    {
        $payload = TraceContextCarrier::decodeJobPayload($job->getRawBody());

        $span->setAttribute('queue.connection', $connectionName);
        $span->setAttribute('queue.name', $job->getQueue() ?: 'default');
        $span->setAttribute('queue.job', $job->resolveName());
        $span->setAttribute('queue.uuid', $job->uuid() ?? '');
        $span->setAttribute('messaging.system', 'laravel');
        $span->setAttribute('messaging.operation', 'process');

        if (isset($payload['attempts']) && is_numeric($payload['attempts'])) {
            $span->setAttribute('queue.attempts', (int) $payload['attempts']);
        }

        $carrier = $payload[TraceContextCarrier::PAYLOAD_KEY] ?? null;

        if (is_array($carrier) && isset($carrier['trace_id']) && is_string($carrier['trace_id'])) {
            $span->setAttribute('trace.parent_trace_id', $carrier['trace_id']);
        }
    }
}
