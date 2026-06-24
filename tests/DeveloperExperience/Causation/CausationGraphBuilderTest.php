<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests\Causation;

use Obeserva\DeveloperExperience\Analysis\SpanCategoryResolver;
use Obeserva\DeveloperExperience\Causation\CausationGraphBuilder;
use Obeserva\DeveloperExperience\Causation\SlowRequestAnalyzer;
use Obeserva\Testing\TraceSnapshotBuilder;
use PHPUnit\Framework\TestCase;

final class CausationGraphBuilderTest extends TestCase
{
    public function test_builds_parent_child_edges(): void
    {
        $snapshots = [
            TraceSnapshotBuilder::make('http.request')->spanId('root')->build(),
            TraceSnapshotBuilder::make('db.select')->spanId('child')->parentSpanId('root')->duration(0.2)->build(),
        ];

        $graph = (new CausationGraphBuilder(new SpanCategoryResolver))->build($snapshots, ['child']);

        $this->assertCount(2, $graph->nodes);
        $this->assertCount(1, $graph->edges);
        $this->assertSame(['child'], $graph->rootCauseSpanIds);
        $this->assertTrue($graph->nodes[1]->isRootCause);
    }
}

final class SlowRequestAnalyzerTest extends TestCase
{
    public function test_detects_slow_request_and_ranks_root_causes(): void
    {
        $snapshots = [
            TraceSnapshotBuilder::make('GET /api')
                ->kind('server')
                ->attribute('http.duration_ms', 2000)
                ->duration(2.0)
                ->spanId('http')
                ->build(),
            TraceSnapshotBuilder::make('db.select')
                ->parentSpanId('http')
                ->spanId('db')
                ->duration(1.2)
                ->build(),
            TraceSnapshotBuilder::make('redis.get')
                ->parentSpanId('http')
                ->spanId('redis')
                ->duration(0.1)
                ->build(),
        ];

        $analyzer = new SlowRequestAnalyzer(new SpanCategoryResolver);

        $this->assertTrue($analyzer->isSlowRequest($snapshots, 1000.0));
        $this->assertSame(['db'], $analyzer->rootCauseSpanIds($snapshots, 1000.0, 1));
    }

    public function test_zero_limit_returns_no_root_cause_span_ids(): void
    {
        $snapshots = [
            TraceSnapshotBuilder::make('GET /api')
                ->kind('server')
                ->attribute('http.duration_ms', 2000)
                ->duration(2.0)
                ->spanId('http')
                ->build(),
            TraceSnapshotBuilder::make('db.select')
                ->parentSpanId('http')
                ->spanId('db')
                ->duration(1.2)
                ->build(),
        ];

        $analyzer = new SlowRequestAnalyzer(new SpanCategoryResolver);

        $this->assertSame([], $analyzer->rootCauseSpanIds($snapshots, 1000.0, 0));
    }
}
