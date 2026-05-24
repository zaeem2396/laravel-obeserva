<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

interface SamplerInterface
{
    public function shouldSample(?string $traceId = null): bool;
}
