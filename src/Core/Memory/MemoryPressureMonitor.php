<?php

declare(strict_types=1);

namespace Obeserva\Core\Memory;

final readonly class MemoryPressureMonitor
{
    public function __construct(
        private int $thresholdBytes,
    ) {}

    public function isUnderPressure(): bool
    {
        if ($this->thresholdBytes <= 0) {
            return false;
        }

        return memory_get_usage(true) >= $this->thresholdBytes;
    }
}
