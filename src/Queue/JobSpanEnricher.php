<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Illuminate\Contracts\Queue\Job;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Laravel\Horizon\HorizonJobPayloadReader;
use Obeserva\Laravel\Horizon\HorizonRetryCorrelator;

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

        $attempt = HorizonJobPayloadReader::retryAttempt($payload);
        $span->setAttribute('queue.attempts', $attempt);
        $span->setAttribute('queue.retry_attempt', $attempt);

        HorizonJobPayloadReader::enrichSpan($span, $payload);
        HorizonRetryCorrelator::enrichSpan($span, $payload);

        $carrier = $payload[TraceContextCarrier::PAYLOAD_KEY] ?? null;

        if (is_array($carrier) && isset($carrier['trace_id']) && is_string($carrier['trace_id'])) {
            $span->setAttribute('trace.parent_trace_id', $carrier['trace_id']);
        }

        $correlationId = TraceContextCarrier::extractCorrelationId($payload);

        if ($correlationId !== null) {
            $span->setAttribute('correlation.id', $correlationId);
        }
    }
}
