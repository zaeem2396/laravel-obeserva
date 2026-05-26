<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;

final readonly class TraceJobProcessedListener
{
    public function __construct(
        private ActiveJobSpanRegistry $jobSpanRegistry,
        private ContextManager $contextManager,
    ) {}

    public function handle(JobProcessed $event): void
    {
        $span = $this->jobSpanRegistry->get();
        if ($span instanceof SpanInterface && ! $span->isEnded()) {
            $span->setAttribute('queue.result', 'success');
            $span->addEvent('queue.job.completed', [
                'queue.job' => $event->job->resolveName(),
            ]);
            $span->end();
        }
        $this->jobSpanRegistry->clear();
        $this->contextManager->clear();
    }
}
