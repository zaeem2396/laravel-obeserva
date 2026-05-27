<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Laravel\Http\RequestSpanEnricher;
use Obeserva\Laravel\Queue\ActiveJobSpanRegistry;

final readonly class TraceJobFailedListener
{
    public function __construct(
        private ActiveJobSpanRegistry $jobSpanRegistry,
        private RequestSpanEnricher $enricher,
        private ContextManager $contextManager,
    ) {}

    public function handle(JobFailed $event): void
    {
        $span = $this->jobSpanRegistry->get();

        if ($span instanceof SpanInterface && ! $span->isEnded()) {
            $span->setAttribute('queue.result', 'failed');
            $this->enricher->enrichException($span, $event->exception);
            $span->addEvent('queue.job.failed');
            $span->end();
        }

        $this->jobSpanRegistry->clear();
        $this->contextManager->clear();
    }
}
