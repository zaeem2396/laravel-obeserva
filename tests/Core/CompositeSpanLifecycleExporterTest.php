<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Export\CompositeSpanLifecycleExporter;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;
use Obeserva\Core\Span\Span;
use PHPUnit\Framework\TestCase;

final class CompositeSpanLifecycleExporterTest extends TestCase
{
    public function test_forwards_lifecycle_events_to_all_exporters(): void
    {
        $first = new RecordingLifecycleExporter;
        $second = new RecordingLifecycleExporter;

        $composite = new CompositeSpanLifecycleExporter([$first, $second]);
        $span = new Span('work', SpanKind::Internal, 'trace', 'span');

        $composite->onSpanStarted($span);
        $composite->onSpanEnded($span);
        $composite->flush();

        $this->assertSame(['work'], $first->started);
        $this->assertSame(['work'], $second->started);
        $this->assertSame(1, $first->flushCount);
        $this->assertSame(1, $second->flushCount);
    }

    public function test_works_with_single_noop_exporter(): void
    {
        $composite = new CompositeSpanLifecycleExporter([new NoopSpanLifecycleExporter]);

        $this->assertInstanceOf(SpanLifecycleExporterInterface::class, $composite);
    }
}
