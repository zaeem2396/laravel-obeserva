<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Memory\CompletedSpanBufferPolicy;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use PHPUnit\Framework\TestCase;

final class TracerCompletedSpanBufferTest extends TestCase
{
    public function test_auto_flushes_when_completed_span_limit_reached(): void
    {
        $exporter = new RecordingLifecycleExporter;
        $tracer = new Tracer(
            new AlwaysOnSampler,
            lifecycleExporter: $exporter,
            bufferPolicy: new CompletedSpanBufferPolicy(2),
        );

        $first = $tracer->startSpan('first', SpanKind::Internal);
        $this->assertInstanceOf(Span::class, $first);
        $first->end();

        $this->assertCount(1, $tracer->completedSpans());
        $this->assertSame(0, $exporter->flushCount);

        $second = $tracer->startSpan('second', SpanKind::Internal);
        $this->assertInstanceOf(Span::class, $second);
        $second->end();

        $this->assertCount(0, $tracer->completedSpans());
        $this->assertSame(1, $exporter->flushCount);
    }
}
