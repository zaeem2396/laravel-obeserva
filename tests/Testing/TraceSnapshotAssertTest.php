<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\Testing\TraceSnapshotAssert;
use Obeserva\Testing\TraceSnapshotBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceSnapshotAssertTest extends TestCase
{
    #[Test]
    public function it_asserts_snapshot_relationships_and_propagation(): void
    {
        $parent = TraceSnapshotBuilder::make('http.request')
            ->kind('server')
            ->traceId('trace-1')
            ->spanId('span-parent')
            ->attribute('http.method', 'GET')
            ->build();

        $child = TraceSnapshotBuilder::make('queue.process')
            ->traceId('trace-1')
            ->spanId('span-child')
            ->parentSpanId('span-parent')
            ->attribute('queue.name', 'default')
            ->build();

        $snapshots = [$parent, $child];

        TraceSnapshotAssert::assertCount(2, $snapshots);
        TraceSnapshotAssert::assertSameTraceId('trace-1', $snapshots);
        TraceSnapshotAssert::assertChildOf('queue.process', 'http.request', $snapshots);
        TraceSnapshotAssert::assertHasAttribute('http.request', 'http.method', 'GET', $snapshots);
        TraceSnapshotAssert::assertPropagationIncludesHttpSpan($snapshots);
        TraceSnapshotAssert::assertPropagationIncludesQueueSpan($snapshots);

        $this->addToAssertionCount(6);
    }
}
