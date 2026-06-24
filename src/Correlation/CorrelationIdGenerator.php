<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Correlation;

final class CorrelationIdGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}
