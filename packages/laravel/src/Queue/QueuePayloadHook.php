<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;

final readonly class QueuePayloadHook
{
    public function __construct(
        private ContextStorageInterface $contextStorage,
        private ActiveSpanStorageInterface $activeSpanStorage,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function __invoke(?string $connection, ?string $queue, array $payload): array
    {
        $context = $this->activeSpanStorage->current() instanceof SpanInterface
            ? $this->buildContextFromActiveSpan()
            : $this->contextStorage->get();

        if (! $context instanceof TraceContextInterface) {
            return $payload;
        }

        /** @var array<string, mixed> $enriched */
        $enriched = TraceContextCarrier::inject($context, $payload);

        return $enriched;
    }

    private function buildContextFromActiveSpan(): ?TraceContextInterface
    {
        $active = $this->activeSpanStorage->current();

        if (! $active instanceof SpanInterface || $active->getTraceId() === '') {
            return $this->contextStorage->get();
        }

        return new TraceContext(
            traceId: $active->getTraceId(),
            spanId: $active->getSpanId(),
            parentSpanId: $active->getParentSpanId(),
            sampled: true,
        );
    }
}
