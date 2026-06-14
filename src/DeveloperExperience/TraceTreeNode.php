<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final readonly class TraceTreeNode
{
    /**
     * @param  list<TraceTreeNode>  $children
     */
    public function __construct(
        public TraceSnapshot $snapshot,
        public array $children = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'snapshot' => $this->snapshot->toArray(),
            'children' => array_map(
                static fn (TraceTreeNode $child): array => $child->toArray(),
                $this->children,
            ),
        ];
    }
}
