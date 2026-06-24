<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Broadcasting;

use Illuminate\Broadcasting\BroadcastEvent;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Events\EventTraceContextCarrier;
use Obeserva\Laravel\Propagation\PropagationContextResolver;

final readonly class TraceBroadcastListener
{
    public function __construct(
        private TracerInterface $tracer,
        private PropagationContextResolver $propagationResolver,
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function handle(BroadcastEvent $broadcastEvent): void
    {
        $event = $broadcastEvent->event;

        if (is_object($event) && (bool) config('obeserva.broadcasts.propagation_enabled', true)) {
            $context = $this->propagationResolver->currentTraceContext();

            if ($context instanceof TraceContextInterface && EventTraceContextCarrier::supports($event)) {
                EventTraceContextCarrier::inject(
                    $event,
                    $context,
                    $this->propagationResolver->currentCorrelationId(),
                );
            }
        }

        $span = $this->tracer->startSpan(
            'broadcast.dispatch:'.(is_object($event) ? class_basename($event) : 'event'),
            SpanKind::Producer,
        );

        if (is_object($event)) {
            $span->setAttribute('broadcast.event_class', $event::class);
        }

        $correlationId = $this->correlationStorage->get();

        if ($correlationId !== null) {
            $span->setAttribute('correlation.id', $correlationId);
        }

        $span->addEvent('broadcast.dispatched');
        $span->end();
    }
}
