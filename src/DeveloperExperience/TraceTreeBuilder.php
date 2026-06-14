<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final class TraceTreeBuilder
{
    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @return list<TraceTreeNode>
     */
    public function buildForest(array $snapshots): array
    {
        if ($snapshots === []) {
            return [];
        }

        /** @var array<string, TraceTreeNode> $nodesById */
        $nodesById = [];

        /** @var array<string, list<string>> $childrenByParent */
        $childrenByParent = [];

        foreach ($snapshots as $snapshot) {
            $nodesById[$snapshot->spanId] = new TraceTreeNode($snapshot);

            if ($snapshot->parentSpanId !== null) {
                $childrenByParent[$snapshot->parentSpanId][] = $snapshot->spanId;
            }
        }

        $buildNode = function (string $spanId) use (&$buildNode, $nodesById, $childrenByParent): TraceTreeNode {
            $snapshot = $nodesById[$spanId]->snapshot;
            $childIds = $childrenByParent[$spanId] ?? [];

            $children = array_map(
                static fn (string $childId): TraceTreeNode => $buildNode($childId),
                $childIds,
            );

            return new TraceTreeNode($snapshot, $children);
        };

        $roots = [];

        foreach ($snapshots as $snapshot) {
            $parentId = $snapshot->parentSpanId;

            if ($parentId === null || ! isset($nodesById[$parentId])) {
                $roots[] = $buildNode($snapshot->spanId);
            }
        }

        return $roots;
    }
}
