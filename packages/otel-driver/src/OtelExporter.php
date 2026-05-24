<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Obeserva\Contracts\Driver\ExporterInterface;

/**
 * Experimental OpenTelemetry exporter — OTLP integration planned for v0.6.0.
 */
final class OtelExporter implements ExporterInterface
{
    public function export(array $spans): void
    {
        // OTLP export implementation planned.
    }
}
