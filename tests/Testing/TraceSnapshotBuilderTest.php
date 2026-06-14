<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\Testing\TraceSnapshotBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceSnapshotBuilderTest extends TestCase
{
    #[Test]
    public function it_builds_trace_snapshots_for_tests(): void
    {
        $snapshot = TraceSnapshotBuilder::make('queue.process')
            ->kind('consumer')
            ->traceId('trace-1')
            ->spanId('span-1')
            ->parentSpanId('parent-1')
            ->attribute('queue.name', 'default')
            ->event('job.started')
            ->build();

        $this->assertSame('queue.process', $snapshot->name);
        $this->assertSame('consumer', $snapshot->kind);
        $this->assertSame('trace-1', $snapshot->traceId);
        $this->assertSame('parent-1', $snapshot->parentSpanId);
        $this->assertSame(['queue.name' => 'default'], $snapshot->attributes);
        $this->assertCount(1, $snapshot->events);
    }
}
