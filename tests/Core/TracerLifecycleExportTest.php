<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use PHPUnit\Framework\TestCase;

final class TracerLifecycleExportTest extends TestCase
{
    public function test_lifecycle_exporter_receives_span_events(): void
    {
        $exporter = new RecordingLifecycleExporter;
        $tracer = new Tracer(new AlwaysOnSampler, lifecycleExporter: $exporter);

        $span = $tracer->startSpan('work');
        $this->assertInstanceOf(Span::class, $span);
        $span->end();

        $this->assertSame(['work'], $exporter->started);
        $this->assertSame(['work'], $exporter->ended);
        $this->assertSame(0, $exporter->flushCount);

        $tracer->flush();
        $this->assertSame(1, $exporter->flushCount);
    }
}

final class RecordingLifecycleExporter implements SpanLifecycleExporterInterface
{
    /** @var list<string> */
    public array $started = [];

    /** @var list<string> */
    public array $ended = [];

    public int $flushCount = 0;

    public function onSpanStarted(SpanInterface $span): void
    {
        $this->started[] = $span->getName();
    }

    public function onSpanEnded(SpanInterface $span): void
    {
        $this->ended[] = $span->getName();
    }

    public function flush(): void
    {
        $this->flushCount++;
    }
}
