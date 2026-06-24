<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

use Obeserva\DeveloperExperience\Analysis\TraceSummary;
use Obeserva\DeveloperExperience\PropagationFlowSummary;
use Obeserva\DeveloperExperience\TraceSnapshot;

final class TelescopeTraceEntryFactory
{
    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @return array<string, mixed>
     */
    public function makeEntry(
        array $snapshots,
        PropagationFlowSummary $propagation,
        ?TraceSummary $summary = null,
    ): array {
        $entry = [
            'type' => 'obeserva-trace',
            'trace_id' => $propagation->traceId,
            'span_count' => $propagation->spanCount,
            'propagation' => $propagation->toArray(),
            'spans' => array_map(
                static fn (TraceSnapshot $snapshot): array => $snapshot->toArray(),
                $snapshots,
            ),
        ];

        if ($summary instanceof TraceSummary) {
            $entry['trace_summary'] = $summary->toArray();
        }

        return $entry;
    }
}
