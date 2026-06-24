<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Core\Propagation\TraceCarrierBag;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceCarrierBagTest extends TestCase
{
    #[Test]
    public function it_injects_and_extracts_trace_context_with_correlation_id(): void
    {
        $context = new TraceContext(
            traceId: 'trace-abc',
            spanId: 'span-producer',
        );

        $carrier = TraceCarrierBag::inject($context, [], 'corr-123');

        $this->assertSame('corr-123', TraceCarrierBag::extractCorrelationId($carrier));

        $extracted = TraceCarrierBag::extract($carrier);

        $this->assertNotNull($extracted);
        $this->assertSame('trace-abc', $extracted->getTraceId());
        $this->assertSame('span-producer', $extracted->getParentSpanId());
    }
}
