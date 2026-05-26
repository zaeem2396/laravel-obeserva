<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use PHPUnit\Framework\TestCase;

final class TracerTest extends TestCase
{
    public function test_nested_spans_share_trace_id(): void
    {
        $context = new ContextManager;
        $tracer = new Tracer(new AlwaysOnSampler, $context, $context);

        $parent = $tracer->startSpan('parent', SpanKind::Server);
        $this->assertInstanceOf(Span::class, $parent);

        $child = $tracer->startSpan('child', SpanKind::Internal);
        $this->assertInstanceOf(Span::class, $child);

        $this->assertSame($parent->getTraceId(), $child->getTraceId());
        $this->assertSame($parent->getSpanId(), $child->getParentSpanId());

        $child->end();
        $parent->end();

        $this->assertCount(2, $tracer->completedSpans());
    }

    public function test_flush_clears_completed_spans(): void
    {
        $tracer = new Tracer(new AlwaysOnSampler);
        $span = $tracer->startSpan('work');
        $this->assertInstanceOf(Span::class, $span);
        $span->end();

        $this->assertCount(1, $tracer->completedSpans());
        $tracer->flush();
        $this->assertCount(0, $tracer->completedSpans());
    }
}
