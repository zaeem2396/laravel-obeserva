<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Tracer as CoreTracer;

/**
 * Scout APM adapter — translates Obeserva spans into Scout transactions.
 */
final readonly class ScoutTracer implements TracerInterface
{
    public function __construct(
        private CoreTracer $coreTracer,
    ) {}

    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface
    {
        return $this->coreTracer->startSpan($name, $kind);
    }

    public function flush(): void
    {
        $this->coreTracer->flush();
    }
}
