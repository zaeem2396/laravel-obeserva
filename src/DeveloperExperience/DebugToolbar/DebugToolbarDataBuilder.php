<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\DebugToolbar;

use Obeserva\DeveloperExperience\Analysis\TraceSummary;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryBuilder;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\TraceSnapshot;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use Obeserva\DeveloperExperience\TraceTreeBuilder;

final readonly class DebugToolbarDataBuilder
{
    public function __construct(
        private TraceSnapshotRegistry $registry,
        private TraceTreeBuilder $treeBuilder,
        private PropagationFlowInspector $propagationInspector,
        private TraceSummaryBuilder $summaryBuilder,
    ) {}

    public function build(): DebugToolbarPayload
    {
        $snapshots = $this->registry->all();
        $totalDurationMs = $this->totalDurationMs($snapshots);
        $traceSummary = $this->buildTraceSummary($snapshots);

        return new DebugToolbarPayload(
            spanCount: count($snapshots),
            totalDurationMs: $totalDurationMs,
            propagation: $this->propagationInspector->summarize($snapshots),
            traceTree: $this->treeBuilder->buildForest($snapshots),
            traceSummary: $traceSummary,
        );
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    private function buildTraceSummary(array $snapshots): ?TraceSummary
    {
        if ($snapshots === [] || ! (bool) config('obeserva.summaries.enabled', true)) {
            return null;
        }

        $threshold = config('obeserva.causation.slow_request_threshold_ms', 1000);
        $topSlowSpans = config('obeserva.summaries.top_slow_spans', 5);

        return $this->summaryBuilder->build(
            $snapshots,
            slowRequestThresholdMs: is_numeric($threshold) ? (float) $threshold : 1000.0,
            topSlowSpans: is_numeric($topSlowSpans) ? (int) $topSlowSpans : 5,
            includeCausation: (bool) config('obeserva.causation.enabled', true),
        );
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
