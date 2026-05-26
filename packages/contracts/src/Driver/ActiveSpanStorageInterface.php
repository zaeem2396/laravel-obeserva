<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

use Obeserva\Contracts\Span\SpanInterface;

interface ActiveSpanStorageInterface
{
    public function current(): ?SpanInterface;

    public function push(SpanInterface $span): void;

    public function pop(): void;
}
