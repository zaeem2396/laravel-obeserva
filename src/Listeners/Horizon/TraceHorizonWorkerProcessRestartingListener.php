<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Horizon;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;

final readonly class TraceHorizonWorkerProcessRestartingListener
{
    public function __construct(
        private TracerInterface $tracer,
        private HorizonThroughputMetrics $metrics,
    ) {}

    public function handle(): void
    {
        $this->metrics->recordWorkerRestart();
        $span = $this->tracer->startSpan('horizon.worker.restarting', SpanKind::Internal);
        $span->setAttribute('messaging.system', 'horizon');
        $span->setAttribute('horizon.event', 'worker_process_restarting');
        $span->addEvent('horizon.worker.restarting');
        $span->end();
    }
}
