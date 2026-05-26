<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Database;

final class QueryCounter
{
    private int $count = 0;

    public function increment(): int
    {
        return ++$this->count;
    }

    public function current(): int
    {
        return $this->count;
    }

    public function reset(): void
    {
        $this->count = 0;
    }
}
