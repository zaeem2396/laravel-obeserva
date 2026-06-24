<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final class TraceSnapshotRegistry
{
    /** @var list<TraceSnapshot> */
    private array $snapshots = [];

    public function __construct(
        private readonly int $maxSnapshots = 0,
    ) {}

    public function record(TraceSnapshot $snapshot): void
    {
        if ($this->maxSnapshots > 0 && count($this->snapshots) >= $this->maxSnapshots) {
            array_shift($this->snapshots);
        }

        $this->snapshots[] = $snapshot;
    }

    /**
     * @return list<TraceSnapshot>
     */
    public function all(): array
    {
        return $this->snapshots;
    }

    public function clear(): void
    {
        $this->snapshots = [];
    }

    public function count(): int
    {
        return count($this->snapshots);
    }
}
