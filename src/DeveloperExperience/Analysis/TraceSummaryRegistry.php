<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

final class TraceSummaryRegistry
{
    private ?TraceSummary $latest = null;

    public function store(TraceSummary $summary): void
    {
        $this->latest = $summary;
    }

    public function latest(): ?TraceSummary
    {
        return $this->latest;
    }

    public function clear(): void
    {
        $this->latest = null;
    }
}
