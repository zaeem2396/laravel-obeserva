<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Causation;

use Obeserva\DeveloperExperience\Analysis\SpanCategoryResolver;
use Obeserva\DeveloperExperience\TraceSnapshot;

final readonly class CausationGraphBuilder
{
    public function __construct(
        private SpanCategoryResolver $categoryResolver,
    ) {}

    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @param  list<string>  $rootCauseSpanIds
     */
    public function build(array $snapshots, array $rootCauseSpanIds = []): CausationGraph
    {
        if ($snapshots === []) {
            return new CausationGraph([], [], []);
        }

        $rootCauseLookup = array_fill_keys($rootCauseSpanIds, true);
        $nodes = [];
        $edges = [];

        foreach ($snapshots as $snapshot) {
            $durationMs = ($snapshot->duration ?? 0.0) * 1000;
            $category = $this->categoryResolver->resolve($snapshot);

            $nodes[] = new CausationNode(
                spanId: $snapshot->spanId,
                name: $snapshot->name,
                category: $category,
                durationMs: round($durationMs, 2),
                isRootCause: isset($rootCauseLookup[$snapshot->spanId]),
            );

            if ($snapshot->parentSpanId !== null) {
                $edges[] = new CausationEdge(
                    fromSpanId: $snapshot->parentSpanId,
                    toSpanId: $snapshot->spanId,
                );
            }
        }

        return new CausationGraph($nodes, $edges, $rootCauseSpanIds);
    }
}
