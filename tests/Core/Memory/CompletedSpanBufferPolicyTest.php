<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests\Memory;

use Obeserva\Core\Memory\CompletedSpanBufferPolicy;
use PHPUnit\Framework\TestCase;

final class CompletedSpanBufferPolicyTest extends TestCase
{
    public function test_disabled_when_max_is_zero(): void
    {
        $policy = new CompletedSpanBufferPolicy(0);

        $this->assertFalse($policy->shouldFlush(1000));
    }

    public function test_flushes_when_limit_reached(): void
    {
        $policy = new CompletedSpanBufferPolicy(3);

        $this->assertFalse($policy->shouldFlush(2));
        $this->assertTrue($policy->shouldFlush(3));
    }
}
