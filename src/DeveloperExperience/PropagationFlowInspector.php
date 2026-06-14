<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final class PropagationFlowInspector
{
    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public function summarize(array $snapshots): PropagationFlowSummary
    {
        if ($snapshots === []) {
            return new PropagationFlowSummary(
                traceId: null,
                spanCount: 0,
                queueSpans: [],
                httpSpans: [],
                hasParentContext: false,
            );
        }

        $traceId = $snapshots[0]->traceId;
        $queueSpans = [];
        $httpSpans = [];
        $hasParentContext = false;

        foreach ($snapshots as $snapshot) {
            if ($snapshot->parentSpanId !== null) {
                $hasParentContext = true;
            }

            if ($this->isQueueSpan($snapshot)) {
                $queueSpans[] = $snapshot->name;
            }

            if ($this->isHttpSpan($snapshot)) {
                $httpSpans[] = $snapshot->name;
            }
        }

        return new PropagationFlowSummary(
            traceId: $traceId,
            spanCount: count($snapshots),
            queueSpans: $queueSpans,
            httpSpans: $httpSpans,
            hasParentContext: $hasParentContext,
        );
    }

    private function isQueueSpan(TraceSnapshot $snapshot): bool
    {
        if (str_starts_with($snapshot->name, 'queue.')) {
            return true;
        }

        return isset($snapshot->attributes['queue.name'])
            || isset($snapshot->attributes['queue.connection']);
    }

    private function isHttpSpan(TraceSnapshot $snapshot): bool
    {
        return $snapshot->kind === 'server'
            || isset($snapshot->attributes['http.method'])
            || isset($snapshot->attributes['http.route']);
    }
}
