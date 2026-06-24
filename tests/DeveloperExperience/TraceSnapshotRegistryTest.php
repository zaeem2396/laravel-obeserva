<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use PHPUnit\Framework\TestCase;

final class TraceSnapshotRegistryTest extends TestCase
{
    public function test_evicts_oldest_snapshot_when_limit_reached(): void
    {
        $registry = new TraceSnapshotRegistry(2);
        $factory = new SpanSnapshotFactory;

        $first = new Span('first', SpanKind::Internal, 'trace', 'span-1');
        $first->end();
        $registry->record($factory->fromSpan($first));

        $second = new Span('second', SpanKind::Internal, 'trace', 'span-2');
        $second->end();
        $registry->record($factory->fromSpan($second));

        $third = new Span('third', SpanKind::Internal, 'trace', 'span-3');
        $third->end();
        $registry->record($factory->fromSpan($third));

        $this->assertSame(2, $registry->count());
        $this->assertSame('second', $registry->all()[0]->name);
        $this->assertSame('third', $registry->all()[1]->name);
    }
}
