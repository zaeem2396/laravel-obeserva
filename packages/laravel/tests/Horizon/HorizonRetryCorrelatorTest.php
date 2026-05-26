<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Horizon;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Horizon\HorizonRetryCorrelator;
use Obeserva\Laravel\Queue\TraceContextCarrier;
use PHPUnit\Framework\TestCase;

final class HorizonRetryCorrelatorTest extends TestCase
{
    public function test_stamps_retry_attempt_and_root_trace_id(): void
    {
        $carrier = HorizonRetryCorrelator::stampRetryAttempt([
            'trace_id' => 'trace-abc',
        ], 3);

        $this->assertSame(3, $carrier['retry_attempt']);
        $this->assertSame('trace-abc', $carrier['root_trace_id']);
    }

    public function test_enriches_span_from_carrier(): void
    {
        $span = new Span('job', SpanKind::Consumer, 'trace', 'span');
        $payload = [
            TraceContextCarrier::PAYLOAD_KEY => [
                'root_trace_id' => 'root-trace',
                'retry_attempt' => 2,
            ],
        ];

        HorizonRetryCorrelator::enrichSpan($span, $payload);

        /** @var array<string, mixed> $attributes */
        $attributes = $span->toArray()['attributes'];
        $this->assertSame('root-trace', $attributes['trace.root_trace_id'] ?? null);
        $this->assertSame(2, $attributes['queue.retry_attempt'] ?? null);
    }
}
