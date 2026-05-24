<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\Testing\FakeTracer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FakeTracerTest extends TestCase
{
    #[Test]
    public function it_records_spans_for_assertions(): void
    {
        $tracer = new FakeTracer;
        $tracer->startSpan('database.query')->end();

        $tracer->assertSpanRecorded('database.query');
        $this->assertCount(1, $tracer->recordedSpans());
    }
}
