<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\Telescope\TelescopeTraceEntryFactory;
use PHPUnit\Framework\TestCase;

final class TelescopeTraceEntryFactoryTest extends TestCase
{
    public function test_builds_telescope_entry_payload(): void
    {
        $factory = new SpanSnapshotFactory;
        $span = new Span('work', SpanKind::Internal, 'trace', 'span');
        $span->end();

        $snapshots = [$factory->fromSpan($span)];
        $propagation = (new PropagationFlowInspector)->summarize($snapshots);

        $entry = (new TelescopeTraceEntryFactory)->makeEntry($snapshots, $propagation);

        $this->assertSame('obeserva-trace', $entry['type']);
        $this->assertSame('trace', $entry['trace_id']);
        $this->assertCount(1, $entry['spans']);
    }
}
