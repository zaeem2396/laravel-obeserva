<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\OtelDriver\OtelSpanKindMapper;
use PHPUnit\Framework\TestCase;

final class OtelSpanKindMapperTest extends TestCase
{
    public function test_maps_span_kinds_to_otel_constants(): void
    {
        $mapper = new OtelSpanKindMapper;

        $this->assertSame('SPAN_KIND_SERVER', $mapper->map(SpanKind::Server));
        $this->assertSame('SPAN_KIND_CLIENT', $mapper->map(SpanKind::Client));
        $this->assertSame('SPAN_KIND_CONSUMER', $mapper->map(SpanKind::Consumer));
        $this->assertSame('SPAN_KIND_PRODUCER', $mapper->map(SpanKind::Producer));
        $this->assertSame('SPAN_KIND_INTERNAL', $mapper->map(SpanKind::Internal));
    }
}
