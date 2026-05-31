<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Span;

interface SpanInterface
{
    public function getName(): string;

    public function getTraceId(): string;

    public function getSpanId(): string;

    public function getParentSpanId(): ?string;

    public function isEnded(): bool;

    public function getDuration(): ?float;

    public function setAttribute(string $key, mixed $value): void;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addEvent(string $name, array $attributes = []): void;

    public function end(): void;
}
