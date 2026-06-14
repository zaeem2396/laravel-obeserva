<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use PHPUnit\Framework\TestCase;

final class SpanSnapshotFactoryTest extends TestCase
{
    public function test_creates_snapshot_from_span(): void
    {
        $span = new Span('http.request', SpanKind::Server, 'trace-1', 'span-1', null, 100.0);
        $span->setAttribute('http.method', 'GET');
        $span->addEvent('request.received');
        $span->end();

        $snapshot = (new SpanSnapshotFactory)->fromSpan($span);

        $this->assertSame('http.request', $snapshot->name);
        $this->assertSame('server', $snapshot->kind);
        $this->assertSame('trace-1', $snapshot->traceId);
        $this->assertSame('span-1', $snapshot->spanId);
        $this->assertSame(['http.method' => 'GET'], $snapshot->attributes);
        $this->assertCount(1, $snapshot->events);
    }
}

final class TraceSnapshotRegistryTest extends TestCase
{
    public function test_records_and_clears_snapshots(): void
    {
        $registry = new TraceSnapshotRegistry;
        $factory = new SpanSnapshotFactory;

        $span = new Span('work', SpanKind::Internal, 'trace', 'span');
        $span->end();

        $registry->record($factory->fromSpan($span));

        $this->assertSame(1, $registry->count());

        $registry->clear();

        $this->assertSame(0, $registry->count());
    }
}
