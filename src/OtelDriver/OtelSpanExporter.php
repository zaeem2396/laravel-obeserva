<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Span\SpanInterface;

final class OtelSpanExporter implements SpanLifecycleExporterInterface
{
    /** @var list<array<string, mixed>> */
    private array $buffer = [];

    public function __construct(
        private readonly OtelExporterClientInterface $client,
        private readonly OtelSpanConverter $converter,
        private readonly OtelConfig $config,
    ) {}

    public function onSpanStarted(SpanInterface $span): void {}

    public function onSpanEnded(SpanInterface $span): void
    {
        if (! $this->shouldExport()) {
            return;
        }

        $this->buffer[] = $this->converter->convert($span);
    }

    public function flush(): void
    {
        if (! $this->shouldExport() || $this->buffer === []) {
            return;
        }

        $this->client->export($this->buffer);
        $this->buffer = [];
    }

    private function shouldExport(): bool
    {
        return $this->config->enabled && $this->client->enabled();
    }
}
