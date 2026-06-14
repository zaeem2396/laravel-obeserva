<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Core\Span\Span;

final class SpanSnapshotFactory
{
    public function fromSpan(SpanInterface $span): TraceSnapshot
    {
        $attributes = $span->getAttributes();
        $events = $span instanceof Span ? $this->eventsFromSpan($span) : [];

        return new TraceSnapshot(
            name: $span->getName(),
            kind: $span->getKind()->value,
            traceId: $span->getTraceId(),
            spanId: $span->getSpanId(),
            parentSpanId: $span->getParentSpanId(),
            startedAt: $span->getStartedAt(),
            endedAt: $span->getEndedAt(),
            duration: $span->getDuration(),
            attributes: $attributes,
            events: $events,
        );
    }

    /**
     * @return list<array{name: string, attributes: array<string, mixed>}>
     */
    private function eventsFromSpan(Span $span): array
    {
        $payload = $span->toArray();

        /** @var list<array{name: string, attributes: array<string, mixed>}> $events */
        $events = $payload['events'];

        return $events;
    }
}
