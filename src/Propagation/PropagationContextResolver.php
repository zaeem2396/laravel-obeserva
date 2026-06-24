<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Propagation;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;

final readonly class PropagationContextResolver
{
    public function __construct(
        private ContextStorageInterface $contextStorage,
        private ActiveSpanStorageInterface $activeSpanStorage,
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function currentTraceContext(): ?TraceContextInterface
    {
        $active = $this->activeSpanStorage->current();

        if ($active instanceof SpanInterface && $active->getTraceId() !== '') {
            return new TraceContext(
                traceId: $active->getTraceId(),
                spanId: $active->getSpanId(),
                parentSpanId: $active->getParentSpanId(),
                sampled: true,
            );
        }

        return $this->contextStorage->get();
    }

    public function currentCorrelationId(): ?string
    {
        return $this->correlationStorage->get();
    }
}
