<?php

declare(strict_types=1);

namespace Obeserva\Core\Export;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;

final class NoopSpanLifecycleExporter implements SpanLifecycleExporterInterface
{
    public function onSpanStarted(SpanInterface $span): void {}

    public function onSpanEnded(SpanInterface $span): void {}

    public function flush(): void {}
}
