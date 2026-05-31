<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

interface ScoutApmClientInterface
{
    public function enabled(): bool;

    public function startSpan(string $operation, ?float $overrideTimestamp = null): void;

    public function stopSpan(): void;

    public function addContext(string $tag, string $value): void;

    public function tagRequest(string $tag, string $value): void;

    public function send(): bool;
}
