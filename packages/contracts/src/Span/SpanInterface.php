<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Span;

interface SpanInterface
{
    public function getName(): string;

    public function setAttribute(string $key, mixed $value): void;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addEvent(string $name, array $attributes = []): void;

    public function end(): void;
}
