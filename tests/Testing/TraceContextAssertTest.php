<?php

declare(strict_types=1);

namespace Obeserva\Testing\Tests;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Laravel\Queue\TraceContextCarrier;
use Obeserva\Testing\TraceContextAssert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceContextAssertTest extends TestCase
{
    #[Test]
    public function it_asserts_matching_trace_contexts(): void
    {
        $context = new TraceContext(
            traceId: '4bf92f3577b34da6a3ce929d0e0e4736',
            spanId: '00f067aa0ba902b7',
            parentSpanId: 'parent-span',
        );

        TraceContextAssert::assertSameTraceContext($context, $context);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_asserts_traceparent_headers(): void
    {
        $context = new TraceContext(
            traceId: '4bf92f3577b34da6a3ce929d0e0e4736',
            spanId: '00f067aa0ba902b7',
        );

        TraceContextAssert::assertTraceparentHeader(
            $context->toPropagationHeaders(),
            $context->getTraceId(),
            $context->getSpanId(),
        );

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_asserts_queue_payload_propagation(): void
    {
        $context = new TraceContext(
            traceId: 'trace-queue',
            spanId: 'producer-span',
        );

        $payload = TraceContextCarrier::inject($context, ['job' => 'dispatch']);

        TraceContextAssert::assertQueuePayloadCarriesContext($payload, $context);
        TraceContextAssert::assertQueuePayloadContainsTraceId($payload, 'trace-queue');

        $this->addToAssertionCount(2);
    }
}
