<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\DeveloperExperience\Analysis\TraceSummary;
use Obeserva\DeveloperExperience\PropagationFlowSummary;
use Obeserva\Testing\TraceSummaryAssert;
use PHPUnit\Framework\TestCase;

final class TraceSummaryAssertTest extends TestCase
{
    public function test_assert_slow_request(): void
    {
        $summary = new TraceSummary(
            traceId: 'trace',
            spanCount: 1,
            totalDurationMs: 100.0,
            categoryCounts: ['http' => 1],
            topSlowSpans: [],
            propagation: new PropagationFlowSummary('trace', 1, [], ['GET /'], false),
            isSlowRequest: true,
            requestDurationMs: 1500.0,
            slowRequestThresholdMs: 1000.0,
            rootCauseSpanIds: ['db'],
            causationGraph: null,
        );

        TraceSummaryAssert::assertSlowRequest($summary);
        TraceSummaryAssert::assertCategoryCount('http', 1, $summary);
        TraceSummaryAssert::assertHasRootCause($summary);

        $this->addToAssertionCount(1);
    }
}
