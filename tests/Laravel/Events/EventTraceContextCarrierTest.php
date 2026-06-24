<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Events;

use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Laravel\Events\Concerns\InteractsWithTraceContext;
use Obeserva\Laravel\Events\EventTraceContextCarrier;
use PHPUnit\Framework\TestCase;

final class SampleTraceEvent
{
    use InteractsWithTraceContext;
}

final class EventTraceContextCarrierTest extends TestCase
{
    public function test_injects_and_extracts_context_on_events(): void
    {
        $event = new SampleTraceEvent;
        $context = new TraceContext(traceId: 'trace-event', spanId: 'span-event');

        EventTraceContextCarrier::inject($event, $context, 'corr-event');

        $extracted = EventTraceContextCarrier::extract($event);

        $this->assertNotNull($extracted);
        $this->assertSame('trace-event', $extracted->getTraceId());
        $this->assertSame('corr-event', EventTraceContextCarrier::extractCorrelationId($event));
        $this->assertTrue(EventTraceContextCarrier::supports($event));
    }
}
