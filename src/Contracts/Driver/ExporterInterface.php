<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

interface ExporterInterface
{
    /**
     * @param  array<int, array<string, mixed>>  $spans
     */
    public function export(array $spans): void;
}
