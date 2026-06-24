<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\DebugToolbar;

use Obeserva\DeveloperExperience\Analysis\TraceSummary;
use Obeserva\DeveloperExperience\PropagationFlowSummary;
use Obeserva\DeveloperExperience\TraceTreeNode;

final readonly class DebugToolbarPayload
{
    /**
     * @param  list<TraceTreeNode>  $traceTree
     */
    public function __construct(
        public int $spanCount,
        public float $totalDurationMs,
        public PropagationFlowSummary $propagation,
        public array $traceTree,
        public ?TraceSummary $traceSummary = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'span_count' => $this->spanCount,
            'total_duration_ms' => $this->totalDurationMs,
            'propagation' => $this->propagation->toArray(),
            'trace_tree' => array_map(
                static fn (TraceTreeNode $node): array => $node->toArray(),
                $this->traceTree,
            ),
        ];

        if ($this->traceSummary instanceof TraceSummary) {
            $payload['trace_summary'] = $this->traceSummary->toArray();
        }

        return $payload;
    }
}
