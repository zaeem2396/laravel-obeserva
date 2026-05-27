<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Tests;

use Obeserva\Contracts\Trace\TraceContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceContextTest extends TestCase
{
    #[Test]
    public function it_serializes_and_deserializes_traceparent_headers(): void
    {
        $context = new TraceContext(
            traceId: '4bf92f3577b34da6a3ce929d0e0e4736',
            spanId: '00f067aa0ba902b7',
            sampled: true,
        );

        $headers = $context->toPropagationHeaders();
        $restored = TraceContext::fromPropagationHeaders($headers);

        $this->assertNotNull($restored);
        $this->assertSame($context->getTraceId(), $restored->getTraceId());
        $this->assertSame($context->getSpanId(), $restored->getSpanId());
        $this->assertTrue($restored->isSampled());
    }
}
