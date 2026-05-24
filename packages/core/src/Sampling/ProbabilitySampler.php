<?php

declare(strict_types=1);

namespace Obeserva\Core\Sampling;

use Obeserva\Contracts\Driver\SamplerInterface;

final class ProbabilitySampler implements SamplerInterface
{
    public function __construct(
        private readonly float $probability = 1.0,
    ) {}

    public function shouldSample(?string $traceId = null): bool
    {
        if ($this->probability >= 1.0) {
            return true;
        }

        if ($this->probability <= 0.0) {
            return false;
        }

        if ($traceId !== null) {
            $hash = crc32($traceId);

            return ($hash % 10000) / 10000.0 < $this->probability;
        }

        return random_int(0, 9999) / 10000.0 < $this->probability;
    }
}
