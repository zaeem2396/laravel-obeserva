<?php

declare(strict_types=1);

namespace Obeserva\Core\Span;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;

final class NoopSpan implements SpanInterface
{
    public function getName(): string
    {
        return '';
    }

    public function getKind(): SpanKind
    {
        return SpanKind::Internal;
    }

    public function getTraceId(): string
    {
        return '';
    }

    public function getSpanId(): string
    {
        return '';
    }

    public function getParentSpanId(): ?string
    {
        return null;
    }

    public function isEnded(): bool
    {
        return false;
    }

    public function getDuration(): ?float
    {
        return null;
    }

    public function setAttribute(string $key, mixed $value): void {}

    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addEvent(string $name, array $attributes = []): void {}

    public function end(): void {}
}
