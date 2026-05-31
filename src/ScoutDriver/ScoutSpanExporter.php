<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;

final class ScoutSpanExporter implements SpanLifecycleExporterInterface
{
    private bool $defaultsApplied = false;

    public function __construct(
        private readonly ScoutApmClientInterface $client,
        private readonly ScoutSpanMapper $mapper,
        private readonly ScoutContextBridge $contextBridge,
        private readonly ScoutConfig $config,
    ) {}

    public function onSpanStarted(SpanInterface $span): void
    {
        if (! $this->shouldExport()) {
            return;
        }

        if (! $this->defaultsApplied) {
            $this->contextBridge->applyDefaultTags();
            $this->defaultsApplied = true;
        }

        $operation = $this->mapper->operation($span->getName(), $span->getKind());
        $this->client->startSpan($operation);
        $this->contextBridge->bridgeSpanContext($span);
    }

    public function onSpanEnded(SpanInterface $span): void
    {
        if (! $this->shouldExport()) {
            return;
        }

        $this->contextBridge->bridgeSpanAttributes($span);
        $this->client->stopSpan();
    }

    public function flush(): void
    {
        if (! $this->shouldExport()) {
            return;
        }

        $this->client->send();
    }

    private function shouldExport(): bool
    {
        return $this->config->enabled && $this->client->enabled();
    }
}
