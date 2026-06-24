<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

final class TraceSummaryJsonFormatter
{
    public function format(TraceSummary $summary): string
    {
        return json_encode($summary->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
