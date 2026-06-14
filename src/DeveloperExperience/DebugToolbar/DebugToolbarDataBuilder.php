<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\DebugToolbar;

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
    ) {}

    public function build(): DebugToolbarPayload
    {
        $snapshots = $this->registry->all();
        $totalDurationMs = $this->totalDurationMs($snapshots);

        return new DebugToolbarPayload(
            spanCount: count($snapshots),
            totalDurationMs: $totalDurationMs,
            propagation: $this->propagationInspector->summarize($snapshots),
            traceTree: $this->treeBuilder->buildForest($snapshots),
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
