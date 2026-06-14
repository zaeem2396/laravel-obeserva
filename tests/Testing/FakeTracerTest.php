<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\Contracts\Span\SpanKind;
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
        $tracer->assertSpanCount(1);
        $this->assertCount(1, $tracer->recordedSpans());
    }

    #[Test]
    public function it_asserts_nested_spans_and_attributes(): void
    {
        $tracer = new FakeTracer;

        $parent = $tracer->startSpan('http.request', SpanKind::Server);
        $parent->setAttribute('http.method', 'GET');

        $child = $tracer->startSpan('database.query');
        $child->setAttribute('db.system', 'mysql');

        $child->end();
        $parent->end();

        $tracer->assertSpanCount(2);
        $tracer->assertChildSpanRecorded('http.request', 'database.query');
        $tracer->assertSpanHasAttribute('http.request', 'http.method', 'GET');
        $tracer->assertSpanHasAttribute('database.query', 'db.system', 'mysql');

        $snapshots = $tracer->spanSnapshots();
        $this->assertCount(2, $snapshots);
        $this->assertSame('http.request', $snapshots[0]->name);
    }
}
