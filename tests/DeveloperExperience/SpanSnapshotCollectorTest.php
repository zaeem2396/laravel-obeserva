<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\SpanSnapshotCollector;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use PHPUnit\Framework\TestCase;

final class SpanSnapshotCollectorTest extends TestCase
{
    public function test_collects_snapshots_on_span_end(): void
    {
        $registry = new TraceSnapshotRegistry;
        $collector = new SpanSnapshotCollector($registry, new SpanSnapshotFactory);

        $span = new Span('db.query', SpanKind::Client, 'trace', 'child', 'parent');
        $collector->onSpanStarted($span);
        $span->end();
        $collector->onSpanEnded($span);

        $this->assertSame(1, $registry->count());
        $this->assertSame('db.query', $registry->all()[0]->name);

        $collector->flush();

        $this->assertSame(0, $registry->count());
    }
}
