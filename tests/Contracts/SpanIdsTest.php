<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Tests;

use Obeserva\Contracts\Trace\SpanIds;
use PHPUnit\Framework\TestCase;

final class SpanIdsTest extends TestCase
{
    public function test_generates_w3c_compatible_identifiers(): void
    {
        $traceId = SpanIds::generateTraceId();
        $spanId = SpanIds::generateSpanId();

        $this->assertSame(32, strlen($traceId));
        $this->assertSame(16, strlen($spanId));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $traceId);
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $spanId);
    }
}
