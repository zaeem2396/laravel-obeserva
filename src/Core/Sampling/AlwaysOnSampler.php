<?php

declare(strict_types=1);

namespace Obeserva\Core\Sampling;

use Obeserva\Contracts\Driver\SamplerInterface;

final class AlwaysOnSampler implements SamplerInterface
{
    public function shouldSample(?string $traceId = null): bool
    {
        return true;
    }
}
