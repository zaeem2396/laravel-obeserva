<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;

interface TracerInterface
{
    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface;

    public function flush(): void;
}
