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

    private int $maxActiveSpanDepth = 0;

    public function configureMaxActiveSpanDepth(int $maxDepth): void
    {
        $this->maxActiveSpanDepth = max(0, $maxDepth);
    }

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
        if ($this->maxActiveSpanDepth > 0 && count($this->activeSpans) >= $this->maxActiveSpanDepth) {
            $orphaned = array_shift($this->activeSpans);

            if (! $orphaned->isEnded()) {
                $orphaned->end();
            }
        }

        $this->activeSpans[] = $span;
    }

    public function pop(): void
    {
        if ($this->activeSpans !== []) {
            array_pop($this->activeSpans);
        }
    }
}
