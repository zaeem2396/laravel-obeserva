<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

use Obeserva\DeveloperExperience\Causation\CausationGraph;
use Obeserva\DeveloperExperience\PropagationFlowSummary;

final readonly class TraceSummary
{
    /**
     * @param  array<string, int>  $categoryCounts
     * @param  list<array{name: string, category: string, duration_ms: float, span_id: string}>  $topSlowSpans
     * @param  list<string>  $rootCauseSpanIds
     */
    public function __construct(
        public ?string $traceId,
        public int $spanCount,
        public float $totalDurationMs,
        public array $categoryCounts,
        public array $topSlowSpans,
        public PropagationFlowSummary $propagation,
        public bool $isSlowRequest,
        public float $requestDurationMs,
        public float $slowRequestThresholdMs,
        public array $rootCauseSpanIds,
        public ?CausationGraph $causationGraph,
    ) {}

    public static function empty(): self
    {
        return new self(
            traceId: null,
            spanCount: 0,
            totalDurationMs: 0.0,
            categoryCounts: [],
            topSlowSpans: [],
            propagation: new PropagationFlowSummary(
                traceId: null,
                spanCount: 0,
                queueSpans: [],
                httpSpans: [],
                hasParentContext: false,
            ),
            isSlowRequest: false,
            requestDurationMs: 0.0,
            slowRequestThresholdMs: 0.0,
            rootCauseSpanIds: [],
            causationGraph: null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'trace_id' => $this->traceId,
            'span_count' => $this->spanCount,
            'total_duration_ms' => $this->totalDurationMs,
            'category_counts' => $this->categoryCounts,
            'top_slow_spans' => $this->topSlowSpans,
            'propagation' => [
                'trace_id' => $this->propagation->traceId,
                'span_count' => $this->propagation->spanCount,
                'queue_spans' => $this->propagation->queueSpans,
                'http_spans' => $this->propagation->httpSpans,
                'has_parent_context' => $this->propagation->hasParentContext,
            ],
            'is_slow_request' => $this->isSlowRequest,
            'request_duration_ms' => $this->requestDurationMs,
            'slow_request_threshold_ms' => $this->slowRequestThresholdMs,
            'root_cause_span_ids' => $this->rootCauseSpanIds,
            'causation_graph' => $this->causationGraph?->toArray(),
        ];
    }
}
