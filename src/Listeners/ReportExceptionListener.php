<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Http\RequestSpanEnricher;

final readonly class ReportExceptionListener
{
    public function __construct(
        private TracerInterface $tracer,
        private ActiveSpanStorageInterface $activeSpanStorage,
        private RequestSpanEnricher $enricher,
    ) {}

    public function report(\Throwable $exception): void
    {
        $span = $this->activeSpanStorage->current();

        if ($span instanceof SpanInterface && ! $span->isEnded()) {
            $this->enricher->enrichException($span, $exception);

            return;
        }

        $span = $this->tracer->startSpan('exception', SpanKind::Internal);
        $this->enricher->enrichException($span, $exception);
        $span->end();
    }
}
