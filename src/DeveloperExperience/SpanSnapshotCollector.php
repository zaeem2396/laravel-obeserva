<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;

final readonly class SpanSnapshotCollector implements SpanLifecycleExporterInterface
{
    public function __construct(
        private TraceSnapshotRegistry $registry,
        private SpanSnapshotFactory $factory,
    ) {}

    public function onSpanStarted(SpanInterface $span): void {}

    public function onSpanEnded(SpanInterface $span): void
    {
        $this->registry->record($this->factory->fromSpan($span));
    }

    public function flush(): void
    {
        $this->registry->clear();
    }
}
