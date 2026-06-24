<?php

declare(strict_types=1);

namespace Obeserva\Core\Memory;

final readonly class CompletedSpanBufferPolicy
{
    public function __construct(
        private int $maxSpans,
    ) {}

    public function shouldFlush(int $completedCount): bool
    {
        return $this->maxSpans > 0 && $completedCount >= $this->maxSpans;
    }
}
