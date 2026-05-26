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
}
