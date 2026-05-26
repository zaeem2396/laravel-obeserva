<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Obeserva\Contracts\Span\SpanInterface;

final class ActiveJobSpanRegistry
{
    private ?SpanInterface $span = null;

    /** @var array<string, mixed> */
    private array $metadata = [];

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function set(SpanInterface $span, array $metadata = []): void
    {
        $this->span = $span;
        $this->metadata = $metadata;
    }

    public function get(): ?SpanInterface
    {
        return $this->span;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function clear(): void
    {
        $this->span = null;
        $this->metadata = [];
    }
}
