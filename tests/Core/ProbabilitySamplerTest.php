<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Sampling\ProbabilitySampler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProbabilitySamplerTest extends TestCase
{
    #[Test]
    public function always_on_sampler_always_samples(): void
    {
        $sampler = new AlwaysOnSampler;

        $this->assertTrue($sampler->shouldSample());
        $this->assertTrue($sampler->shouldSample('trace-abc'));
    }

    #[Test]
    public function probability_sampler_is_deterministic_for_trace_id(): void
    {
        $sampler = new ProbabilitySampler(0.5);
        $first = $sampler->shouldSample('deterministic-trace-id');
        $second = $sampler->shouldSample('deterministic-trace-id');

        $this->assertSame($first, $second);
    }
}
