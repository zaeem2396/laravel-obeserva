<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Queue;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Laravel\Queue\TraceContextCarrier;
use PHPUnit\Framework\TestCase;

final class TraceContextCarrierTest extends TestCase
{
    public function test_inject_and_extract_round_trip(): void
    {
        $context = new TraceContext('a'.str_repeat('b', 31), str_repeat('c', 16), null, true);
        $payload = TraceContextCarrier::inject($context, ['job' => 'test']);

        $this->assertArrayHasKey(TraceContextCarrier::PAYLOAD_KEY, $payload);

        /** @var array<string, mixed> $payload */
        $restored = TraceContextCarrier::extract($payload);

        $this->assertNotNull($restored);
        $this->assertSame($context->getTraceId(), $restored->getTraceId());
        $this->assertSame($context->getSpanId(), $restored->getParentSpanId());
    }

    public function test_extract_fallback_uses_parent_span_id_when_traceparent_invalid(): void
    {
        $traceId = 'a'.str_repeat('b', 31);
        $parentSpanId = str_repeat('c', 16);

        $payload = [
            TraceContextCarrier::PAYLOAD_KEY => [
                'trace_id' => $traceId,
                'parent_span_id' => $parentSpanId,
            ],
        ];

        $restored = TraceContextCarrier::extract($payload);

        $this->assertNotNull($restored);
        $this->assertSame($traceId, $restored->getTraceId());
        $this->assertSame($parentSpanId, $restored->getSpanId());
        $this->assertSame($parentSpanId, $restored->getParentSpanId());
    }
}
