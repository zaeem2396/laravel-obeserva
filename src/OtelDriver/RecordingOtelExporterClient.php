<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

final class RecordingOtelExporterClient implements OtelExporterClientInterface
{
    /** @var list<array<string, mixed>> */
    public array $exportedSpans = [];

    public function __construct(
        private readonly bool $enabled = true,
    ) {}

    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param  list<array<string, mixed>>  $spans
     */
    public function export(array $spans): bool
    {
        if (! $this->enabled) {
            return false;
        }

        array_push($this->exportedSpans, ...$spans);

        return true;
    }
}
