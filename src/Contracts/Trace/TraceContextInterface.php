<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Trace;

interface TraceContextInterface
{
    public function getTraceId(): string;

    public function getSpanId(): string;

    public function getParentSpanId(): ?string;

    public function isSampled(): bool;

    /**
     * @return array<string, string>
     */
    public function toPropagationHeaders(): array;

    /**
     * @param  array<string, mixed>  $headers
     */
    public static function fromPropagationHeaders(array $headers): ?self;
}
