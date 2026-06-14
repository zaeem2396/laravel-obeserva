<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\SpanIds;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshot;
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

    /**
     * @return list<TraceSnapshot>
     */
    public function spanSnapshots(): array
    {
        $factory = new SpanSnapshotFactory;

        return array_map(
            $factory->fromSpan(...),
            $this->spans,
        );
    }

    public function findSpan(string $name): ?Span
    {
        foreach ($this->spans as $span) {
            if ($span->getName() === $name) {
                return $span;
            }
        }

        return null;
    }

    public function assertSpanRecorded(string $name): void
    {
        if ($this->findSpan($name) instanceof Span) {
            return;
        }

        throw new AssertionFailedError(
            sprintf('Expected span [%s] was not recorded.', $name)
        );
    }

    public function assertSpanCount(int $expected): void
    {
        $actual = count($this->spans);

        if ($actual !== $expected) {
            throw new AssertionFailedError(sprintf(
                'Expected %d recorded spans, got %d.',
                $expected,
                $actual,
            ));
        }
    }

    public function assertSpanHasAttribute(string $name, string $key, mixed $value): void
    {
        $span = $this->findSpan($name);

        if (! $span instanceof Span) {
            throw new AssertionFailedError(sprintf('Expected span [%s] was not recorded.', $name));
        }

        if (($span->getAttributes()[$key] ?? null) !== $value) {
            throw new AssertionFailedError(sprintf(
                'Expected span [%s] attribute [%s] to equal [%s].',
                $name,
                $key,
                is_scalar($value) || $value === null ? (string) $value : json_encode($value),
            ));
        }
    }

    public function assertChildSpanRecorded(string $parentName, string $childName): void
    {
        $parent = $this->findSpan($parentName);
        $child = $this->findSpan($childName);

        if (! $parent instanceof Span) {
            throw new AssertionFailedError(sprintf('Expected parent span [%s] was not recorded.', $parentName));
        }

        if (! $child instanceof Span) {
            throw new AssertionFailedError(sprintf('Expected child span [%s] was not recorded.', $childName));
        }

        if ($child->getParentSpanId() !== $parent->getSpanId()) {
            throw new AssertionFailedError(sprintf(
                'Expected span [%s] to be a child of [%s].',
                $childName,
                $parentName,
            ));
        }
    }
}
