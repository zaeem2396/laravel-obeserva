<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Horizon;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;

final readonly class TraceHorizonSupervisorProcessRestartingListener
{
    public function __construct(
        private TracerInterface $tracer,
        private HorizonThroughputMetrics $metrics,
    ) {}

    public function handle(): void
    {
        $this->metrics->recordSupervisorRestart();
        $span = $this->tracer->startSpan('horizon.supervisor.restarting', SpanKind::Internal);
        $span->setAttribute('messaging.system', 'horizon');
        $span->setAttribute('horizon.event', 'supervisor_process_restarting');
        $span->addEvent('horizon.supervisor.restarting');
        $span->end();
    }
}
