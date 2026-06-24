<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests\Analysis;

use Obeserva\DeveloperExperience\Analysis\SpanCategoryResolver;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryBuilder;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryJsonFormatter;
use Obeserva\DeveloperExperience\Causation\CausationGraphBuilder;
use Obeserva\DeveloperExperience\Causation\SlowRequestAnalyzer;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\Testing\TraceSnapshotBuilder;
use PHPUnit\Framework\TestCase;

final class TraceSummaryBuilderTest extends TestCase
{
    public function test_builds_summary_with_slow_request_root_causes(): void
    {
        $snapshots = [
            TraceSnapshotBuilder::make('GET users.index')
                ->kind('server')
                ->attribute('http.method', 'GET')
                ->attribute('http.duration_ms', 1500)
                ->duration(1.5)
                ->spanId('http')
                ->build(),
            TraceSnapshotBuilder::make('db.select')
                ->parentSpanId('http')
                ->spanId('db-slow')
                ->duration(0.9)
                ->build(),
            TraceSnapshotBuilder::make('cache.get')
                ->parentSpanId('http')
                ->spanId('cache')
                ->duration(0.05)
                ->build(),
        ];

        $builder = new TraceSummaryBuilder(
            new SpanCategoryResolver,
            new PropagationFlowInspector,
            new SlowRequestAnalyzer(new SpanCategoryResolver),
            new CausationGraphBuilder(new SpanCategoryResolver),
        );

        $summary = $builder->build($snapshots, slowRequestThresholdMs: 1000.0, topSlowSpans: 3);

        $this->assertTrue($summary->isSlowRequest);
        $this->assertSame(1500.0, $summary->requestDurationMs);
        $this->assertSame(['db-slow'], $summary->rootCauseSpanIds);
        $this->assertSame(1, $summary->categoryCounts['database'] ?? 0);
        $this->assertSame(1, $summary->categoryCounts['cache'] ?? 0);
        $this->assertNotNull($summary->causationGraph);
        $this->assertStringContainsString('"is_slow_request": true', (new TraceSummaryJsonFormatter)->format($summary));
    }
}
