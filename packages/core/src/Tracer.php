<?php

declare(strict_types=1);

namespace Obeserva\Core;

use Obeserva\Contracts\Driver\SamplerInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\NoopSpan;
use Obeserva\Core\Span\Span;

final class Tracer implements TracerInterface
{
    public function __construct(
        private readonly SamplerInterface $sampler,
    ) {}

    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface
    {
        if (! $this->sampler->shouldSample()) {
            return new NoopSpan;
        }

        return new Span($name, $kind);
    }

    public function flush(): void {}
}
