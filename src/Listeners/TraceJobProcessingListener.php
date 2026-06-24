<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;
use Obeserva\Laravel\Queue\JobSpanEnricher;
use Obeserva\Laravel\Queue\TraceContextCarrier;

final readonly class TraceJobProcessingListener
{
    public function __construct(
        private TracerInterface $tracer,
        private ContextStorageInterface $contextStorage,
        private ActiveJobSpanRegistry $jobSpanRegistry,
        private JobSpanEnricher $jobSpanEnricher,
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function handle(JobProcessing $event): void
    {
        $payload = TraceContextCarrier::decodeJobPayload($event->job->getRawBody());
        $incoming = TraceContextCarrier::extract($payload);

        if ($incoming instanceof TraceContextInterface) {
            $this->contextStorage->set($incoming);
        }

        $correlationId = TraceContextCarrier::extractCorrelationId($payload);

        if ($correlationId !== null) {
            $this->correlationStorage->set($correlationId);
        }

        $span = $this->tracer->startSpan(
            'queue.process:'.$event->job->resolveName(),
            SpanKind::Consumer,
        );

        $this->jobSpanEnricher->enrich($span, $event->job, $event->connectionName);
        $span->addEvent('queue.job.started');

        $this->jobSpanRegistry->set($span, [
            'connection' => $event->connectionName,
            'job' => $event->job->resolveName(),
        ]);
    }
}
