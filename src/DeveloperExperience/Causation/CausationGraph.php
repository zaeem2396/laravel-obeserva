<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Causation;

final readonly class CausationGraph
{
    /**
     * @param  list<CausationNode>  $nodes
     * @param  list<CausationEdge>  $edges
     * @param  list<string>  $rootCauseSpanIds
     */
    public function __construct(
        public array $nodes,
        public array $edges,
        public array $rootCauseSpanIds,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'nodes' => array_map(
                static fn (CausationNode $node): array => $node->toArray(),
                $this->nodes,
            ),
            'edges' => array_map(
                static fn (CausationEdge $edge): array => $edge->toArray(),
                $this->edges,
            ),
            'root_cause_span_ids' => $this->rootCauseSpanIds,
        ];
    }
}
