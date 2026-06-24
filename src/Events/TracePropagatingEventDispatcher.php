<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Propagation\PropagationContextResolver;

final readonly class TracePropagatingEventDispatcher implements Dispatcher
{
    public function __construct(
        private Dispatcher $dispatcher,
        private PropagationContextResolver $propagationResolver,
        private ContextStorageInterface $contextStorage,
        private CorrelationContextStorage $correlationStorage,
        /** @var \Closure(): TracerInterface */
        private \Closure $tracerFactory,
        private bool $propagationEnabled,
        private bool $tracingEnabled,
    ) {}

    public function listen(mixed $events, mixed $listener = null): void
    {
        $this->dispatcher->listen($events, $listener);
    }

    public function hasListeners($eventName): bool
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    public function subscribe($subscriber): void
    {
        $this->dispatcher->subscribe($subscriber);
    }

    public function until($event, $payload = [])
    {
        return $this->dispatch($event, $payload, true);
    }

    public function dispatch(mixed $event, mixed $payload = [], mixed $halt = false): mixed
    {
        if (! is_object($event)) {
            return $this->dispatcher->dispatch($event, $payload, $halt);
        }

        if (! $this->shouldInstrument($event)) {
            return $this->dispatcher->dispatch($event, $payload, $halt);
        }

        $span = null;
        $previousContext = $this->contextStorage->get();
        $previousCorrelation = $this->correlationStorage->get();

        $incoming = EventTraceContextCarrier::extract($event);

        if ($incoming instanceof TraceContextInterface) {
            $this->contextStorage->set($incoming);
        }

        $incomingCorrelation = EventTraceContextCarrier::extractCorrelationId($event);

        if ($incomingCorrelation !== null) {
            $this->correlationStorage->set($incomingCorrelation);
        }

        if ($this->propagationEnabled) {
            $context = $this->propagationResolver->currentTraceContext();

            if ($context instanceof TraceContextInterface) {
                EventTraceContextCarrier::inject(
                    $event,
                    $context,
                    $this->propagationResolver->currentCorrelationId(),
                );
            }
        }

        if ($this->tracingEnabled) {
            $span = ($this->tracerFactory)()->startSpan('event.dispatch:'.class_basename($event), SpanKind::Internal);
            $span->setAttribute('event.class', $event::class);

            $correlationId = $this->correlationStorage->get();

            if ($correlationId !== null) {
                $span->setAttribute('correlation.id', $correlationId);
            }
        }

        try {
            return $this->dispatcher->dispatch($event, $payload, $halt);
        } finally {
            $span?->end();
            $this->contextStorage->set($previousContext);
            $this->correlationStorage->set($previousCorrelation);
        }
    }

    public function push(mixed $event, mixed $payload = []): void
    {
        $this->dispatcher->push($event, $payload);
    }

    public function flush($event): void
    {
        $this->dispatcher->flush($event);
    }

    public function forget($event): void
    {
        $this->dispatcher->forget($event);
    }

    public function forgetPushed(): void
    {
        $this->dispatcher->forgetPushed();
    }

    private function shouldInstrument(object $event): bool
    {
        $class = $event::class;

        return ! str_starts_with($class, 'Illuminate\\')
            && ! str_starts_with($class, 'Orchestra\\')
            && ! str_starts_with($class, 'Symfony\\');
    }
}
