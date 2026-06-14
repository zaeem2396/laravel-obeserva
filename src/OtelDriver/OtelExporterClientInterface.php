<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

interface OtelExporterClientInterface
{
    public function enabled(): bool;

    /**
     * @param  list<array<string, mixed>>  $spans
     */
    public function export(array $spans): bool;
}
