<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Horizon;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Horizon\ActiveHorizonSupervisorRegistry;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;

final readonly class TraceHorizonSupervisorLoopedListener
{
    public function __construct(
        private TracerInterface $tracer,
        private ActiveHorizonSupervisorRegistry $supervisorRegistry,
        private HorizonThroughputMetrics $metrics,
    ) {}

    public function handle(object $event): void
    {
        $supervisor = $this->readSupervisor($event);

        if ($supervisor === null) {
            return;
        }

        $name = $this->readSupervisorName($supervisor);

        if ($name === null) {
            return;
        }

        $span = $this->supervisorRegistry->get($name);

        if (! $span instanceof SpanInterface || $span->isEnded()) {
            $span = $this->tracer->startSpan('horizon.supervisor:'.$name, SpanKind::Internal);
            $span->setAttribute('horizon.supervisor', $name);
            $span->setAttribute('messaging.system', 'horizon');
            $span->addEvent('horizon.supervisor.started');
            $this->supervisorRegistry->set($name, $span);
        }

        foreach ($this->metrics->toAttributes() as $key => $value) {
            $span->setAttribute($key, $value);
        }

        $span->addEvent('horizon.supervisor.looped');
    }

    private function readSupervisor(object $event): ?object
    {
        if (! property_exists($event, 'supervisor')) {
            return null;
        }

        $supervisor = $event->supervisor;

        return is_object($supervisor) ? $supervisor : null;
    }

    private function readSupervisorName(object $supervisor): ?string
    {
        if (! property_exists($supervisor, 'name')) {
            return null;
        }

        $name = $supervisor->name;

        return is_string($name) && $name !== '' ? $name : null;
    }
}
