<?php

declare(strict_types=1);

namespace Obeserva\Core\Context;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Trace\TraceContextInterface;

final class ContextManager implements ActiveSpanStorageInterface, ContextStorageInterface
{
    private ?TraceContextInterface $context = null;

    /** @var list<SpanInterface> */
    private array $activeSpans = [];

    public function get(): ?TraceContextInterface
    {
        return $this->context;
    }

    public function set(?TraceContextInterface $context): void
    {
        $this->context = $context;
    }

    public function clear(): void
    {
        $this->context = null;
        $this->activeSpans = [];
    }

    public function current(): ?SpanInterface
    {
        if ($this->activeSpans === []) {
            return null;
        }

        return $this->activeSpans[array_key_last($this->activeSpans)];
    }

    public function push(SpanInterface $span): void
    {
        $this->activeSpans[] = $span;
    }

    public function pop(): void
    {
        if ($this->activeSpans !== []) {
            array_pop($this->activeSpans);
        }
    }
}
