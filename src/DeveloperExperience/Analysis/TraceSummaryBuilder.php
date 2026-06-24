<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

use Obeserva\DeveloperExperience\Causation\CausationGraphBuilder;
use Obeserva\DeveloperExperience\Causation\SlowRequestAnalyzer;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\TraceSnapshot;

final readonly class TraceSummaryBuilder
{
    public function __construct(
        private SpanCategoryResolver $categoryResolver,
        private PropagationFlowInspector $propagationInspector,
        private SlowRequestAnalyzer $slowRequestAnalyzer,
        private CausationGraphBuilder $causationGraphBuilder,
    ) {}

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public function build(
        array $snapshots,
        float $slowRequestThresholdMs,
        int $topSlowSpans = 5,
        bool $includeCausation = true,
    ): TraceSummary {
        if ($snapshots === []) {
            return TraceSummary::empty();
        }

        $propagation = $this->propagationInspector->summarize($snapshots);
        $categoryCounts = $this->categoryCounts($snapshots);
        $topSlow = $this->topSlowSpans($snapshots, $topSlowSpans);
        $requestDurationMs = $this->slowRequestAnalyzer->requestDurationMs($snapshots);
        $isSlow = $this->slowRequestAnalyzer->isSlowRequest($snapshots, $slowRequestThresholdMs);
        $rootCauseSpanIds = $isSlow
            ? $this->slowRequestAnalyzer->rootCauseSpanIds($snapshots, $slowRequestThresholdMs, $topSlowSpans)
            : [];

        $causationGraph = $includeCausation
            ? $this->causationGraphBuilder->build($snapshots, $rootCauseSpanIds)
            : null;

        return new TraceSummary(
            traceId: $propagation->traceId,
            spanCount: count($snapshots),
            totalDurationMs: $this->totalDurationMs($snapshots),
            categoryCounts: $categoryCounts,
            topSlowSpans: $topSlow,
            propagation: $propagation,
            isSlowRequest: $isSlow,
            requestDurationMs: $requestDurationMs,
            slowRequestThresholdMs: $slowRequestThresholdMs,
            rootCauseSpanIds: $rootCauseSpanIds,
            causationGraph: $causationGraph,
        );
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @return array<string, int>
     */
    private function categoryCounts(array $snapshots): array
    {
        $counts = [];

        foreach ($snapshots as $snapshot) {
            $category = $this->categoryResolver->resolve($snapshot)->value;
            $counts[$category] = ($counts[$category] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @return list<array{name: string, category: string, duration_ms: float, span_id: string}>
     */
    private function topSlowSpans(array $snapshots, int $limit): array
    {
        $ranked = [];

        foreach ($snapshots as $snapshot) {
            if ($snapshot->duration === null || $snapshot->duration <= 0) {
                continue;
            }

            $ranked[] = [
                'span_id' => $snapshot->spanId,
                'name' => $snapshot->name,
                'category' => $this->categoryResolver->resolve($snapshot)->value,
                'duration_ms' => round($snapshot->duration * 1000, 2),
            ];
        }

        usort(
            $ranked,
            static fn (array $left, array $right): int => $right['duration_ms'] <=> $left['duration_ms'],
        );

        if ($limit <= 0) {
            return [];
        }

        return array_slice($ranked, 0, $limit);
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    private function totalDurationMs(array $snapshots): float
    {
        $total = 0.0;

        foreach ($snapshots as $snapshot) {
            if ($snapshot->duration !== null) {
                $total += $snapshot->duration * 1000;
            }
        }

        return round($total, 2);
    }
}
