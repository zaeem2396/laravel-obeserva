<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\DeveloperExperience\Analysis\TraceSummary;
use Obeserva\DeveloperExperience\Causation\CausationGraph;
use PHPUnit\Framework\AssertionFailedError;

final class TraceSummaryAssert
{
    public static function assertSlowRequest(TraceSummary $summary): void
    {
        if (! $summary->isSlowRequest) {
            throw new AssertionFailedError('Expected a slow request trace summary.');
        }
    }

    public static function assertNotSlowRequest(TraceSummary $summary): void
    {
        if ($summary->isSlowRequest) {
            throw new AssertionFailedError('Expected a non-slow request trace summary.');
        }
    }

    public static function assertCategoryCount(string $category, int $expected, TraceSummary $summary): void
    {
        $actual = $summary->categoryCounts[$category] ?? 0;

        if ($actual !== $expected) {
            throw new AssertionFailedError(sprintf(
                'Expected %d spans in category "%s", got %d.',
                $expected,
                $category,
                $actual,
            ));
        }
    }

    public static function assertHasRootCause(TraceSummary $summary): void
    {
        if ($summary->rootCauseSpanIds === []) {
            throw new AssertionFailedError('Expected at least one root cause span id.');
        }
    }

    public static function assertHasCausationGraph(TraceSummary $summary): void
    {
        if (! $summary->causationGraph instanceof CausationGraph) {
            throw new AssertionFailedError('Expected a causation graph on the trace summary.');
        }
    }
}
