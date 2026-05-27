<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\SpanIds;
use Obeserva\Core\Span\Span;
use PHPUnit\Framework\AssertionFailedError;

final class FakeTracer implements TracerInterface
{
    /** @var list<Span> */
    private array $spans = [];

    private ?string $traceId = null;

    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface
    {
        $this->traceId ??= SpanIds::generateTraceId();
        $parentSpanId = $this->spans !== [] ? $this->spans[array_key_last($this->spans)]->getSpanId() : null;

        $span = new Span(
            $name,
            $kind,
            $this->traceId,
            SpanIds::generateSpanId(),
            $parentSpanId,
        );

        $this->spans[] = $span;

        return $span;
    }

    public function flush(): void
    {
        $this->spans = [];
        $this->traceId = null;
    }

    /**
     * @return list<Span>
     */
    public function recordedSpans(): array
    {
        return $this->spans;
    }

    public function assertSpanRecorded(string $name): void
    {
        foreach ($this->spans as $span) {
            if ($span->getName() === $name) {
                return;
            }
        }

        throw new AssertionFailedError(
            sprintf('Expected span [%s] was not recorded.', $name)
        );
    }
}
