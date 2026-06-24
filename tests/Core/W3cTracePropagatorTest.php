<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Core\Propagation\W3cTracePropagator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class W3cTracePropagatorTest extends TestCase
{
    #[Test]
    public function it_propagates_via_w3c_headers(): void
    {
        $propagator = new W3cTracePropagator;
        $context = new TraceContext(
            traceId: '4bf92f3577b34da6a3ce929d0e0e4736',
            spanId: '00f067aa0ba902b7',
        );

        $carrier = $propagator->inject($context, []);
        $extracted = $propagator->extract($carrier);

        $this->assertNotNull($extracted);
        $this->assertSame($context->getTraceId(), $extracted->getTraceId());
    }
}
