<?php

declare(strict_types=1);

namespace Obeserva\Core;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\SamplerInterface;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\SpanIds;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Core\Memory\CompletedSpanBufferPolicy;
use Obeserva\Core\Memory\MemoryPressureMonitor;
use Obeserva\Core\Span\NoopSpan;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Span\SpanScope;

final class Tracer implements TracerInterface
{
    /** @var list<Span> */
    private array $completedSpans = [];

    public function __construct(
        private readonly SamplerInterface $sampler,
        private readonly ?ContextStorageInterface $contextStorage = null,
        private readonly ?ActiveSpanStorageInterface $activeSpanStorage = null,
        private readonly ?SpanLifecycleExporterInterface $lifecycleExporter = null,
        private readonly ?CompletedSpanBufferPolicy $bufferPolicy = null,
        private readonly ?MemoryPressureMonitor $memoryPressureMonitor = null,
    ) {}

    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface
    {
        if (! $this->sampler->shouldSample()) {
            return new NoopSpan;
        }

        $traceId = $this->resolveTraceId();
        $parentSpanId = $this->resolveParentSpanId();
        $spanId = SpanIds::generateSpanId();

        $span = new Span($name, $kind, $traceId, $spanId, $parentSpanId);

        $span->whenEnded(function (Span $ended): void {
            $this->activeSpanStorage?->pop();
            $this->lifecycleExporter?->onSpanEnded($ended);
            $this->completedSpans[] = $ended;

            if ($this->shouldAutoFlush()) {
                $this->flush();
            }
        });

        $this->contextStorage?->set(new TraceContext(
            traceId: $traceId,
            spanId: $spanId,
            parentSpanId: $parentSpanId,
            sampled: true,
        ));

        $this->activeSpanStorage?->push($span);

        $this->lifecycleExporter?->onSpanStarted($span);

        return $span;
    }

    public function trace(string $name, SpanKind $kind = SpanKind::Internal): SpanScope
    {
        $span = $this->startSpan($name, $kind);

        return new SpanScope($span, fn (): mixed => null);
    }

    /**
     * @return list<Span>
     */
    public function completedSpans(): array
    {
        return $this->completedSpans;
    }

    public function flush(): void
    {
        $this->lifecycleExporter?->flush();
        $this->completedSpans = [];
    }

    private function shouldAutoFlush(): bool
    {
        $completedCount = count($this->completedSpans);

        if ($this->bufferPolicy?->shouldFlush($completedCount) === true) {
            return true;
        }

        return $this->memoryPressureMonitor?->isUnderPressure() ?? false;
    }

    private function resolveTraceId(): string
    {
        $context = $this->contextStorage?->get();

        if ($context instanceof TraceContextInterface) {
            return $context->getTraceId();
        }

        return SpanIds::generateTraceId();
    }

    private function resolveParentSpanId(): ?string
    {
        $active = $this->activeSpanStorage?->current();

        if ($active instanceof SpanInterface && $active->getSpanId() !== '') {
            return $active->getSpanId();
        }

        return $this->contextStorage?->get()?->getSpanId();
    }
}
