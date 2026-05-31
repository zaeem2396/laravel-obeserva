<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

use Obeserva\Contracts\Span\SpanInterface;

interface SpanLifecycleExporterInterface
{
    public function onSpanStarted(SpanInterface $span): void;

    public function onSpanEnded(SpanInterface $span): void;

    public function flush(): void;
}
