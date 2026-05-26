<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use PHPUnit\Framework\TestCase;

final class SpanTest extends TestCase
{
    public function test_end_is_idempotent_and_records_duration(): void
    {
        $span = new Span('query', SpanKind::Internal, 'trace', 'span', null, 1000.0);
        $ended = false;

        $span->whenEnded(function () use (&$ended): void {
            $ended = true;
        });

        $span->end();
        $span->end();

        $this->assertTrue($ended);
        $this->assertTrue($span->isEnded());
        $this->assertNotNull($span->getDuration());
    }

    public function test_to_array_includes_trace_metadata(): void
    {
        $span = new Span('http', SpanKind::Server, 'abc', 'def', 'parent');
        $span->setAttribute('http.method', 'GET');
        $span->end();

        $data = $span->toArray();
        $attributes = $data['attributes'];
        $this->assertIsArray($attributes);

        $this->assertSame('http', $data['name']);
        $this->assertSame('abc', $data['trace_id']);
        $this->assertSame('def', $data['span_id']);
        $this->assertSame('parent', $data['parent_span_id']);
        $this->assertSame('GET', $attributes['http.method']);
    }
}
