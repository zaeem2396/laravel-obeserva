<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use PHPUnit\Framework\TestCase;

final class ContextManagerTest extends TestCase
{
    public function test_active_span_stack_tracks_current_span(): void
    {
        $manager = new ContextManager;
        $outer = new Span('outer', SpanKind::Internal, 't', 's1');
        $inner = new Span('inner', SpanKind::Internal, 't', 's2', 's1');

        $manager->push($outer);
        $this->assertSame($outer, $manager->current());

        $manager->push($inner);
        $this->assertSame($inner, $manager->current());

        $manager->pop();
        $this->assertSame($outer, $manager->current());

        $manager->clear();
        $this->assertNull($manager->current());
        $this->assertNull($manager->get());
    }

    public function test_trace_context_round_trip(): void
    {
        $manager = new ContextManager;
        $context = new TraceContext('trace', 'span', null, true);

        $manager->set($context);

        $this->assertSame('trace', $manager->get()?->getTraceId());
    }
}
