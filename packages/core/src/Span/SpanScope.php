<?php

declare(strict_types=1);

namespace Obeserva\Core\Span;

use Obeserva\Contracts\Span\SpanInterface;

final class SpanScope
{
    private bool $detached = false;

    public function __construct(
        public readonly SpanInterface $span,
        private readonly \Closure $onDetach,
    ) {}

    public function __destruct()
    {
        $this->detach();
    }

    public function detach(): void
    {
        if ($this->detached) {
            return;
        }

        $this->detached = true;

        if (! $this->span->isEnded()) {
            $this->span->end();
        }

        ($this->onDetach)();
    }
}
