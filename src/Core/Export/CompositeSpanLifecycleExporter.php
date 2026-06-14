<?php

declare(strict_types=1);

namespace Obeserva\Core\Export;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;

final readonly class CompositeSpanLifecycleExporter implements SpanLifecycleExporterInterface
{
    /**
     * @param  list<SpanLifecycleExporterInterface>  $exporters
     */
    public function __construct(
        private array $exporters,
    ) {}

    public function onSpanStarted(SpanInterface $span): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->onSpanStarted($span);
        }
    }

    public function onSpanEnded(SpanInterface $span): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->onSpanEnded($span);
        }
    }

    public function flush(): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->flush();
        }
    }
}
