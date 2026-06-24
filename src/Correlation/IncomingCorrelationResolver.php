<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Correlation;

use Illuminate\Http\Request;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Trace\TraceContextInterface;

final readonly class IncomingCorrelationResolver
{
    public function __construct(
        private CorrelationContextStorage $correlationStorage,
        private CorrelationIdGenerator $generator,
    ) {}

    public function resolveFromRequest(Request $request, string $header = 'X-Correlation-ID'): string
    {
        $incoming = $request->headers->get($header);

        if (is_string($incoming) && $incoming !== '') {
            $this->correlationStorage->set($incoming);

            return $incoming;
        }

        if ($this->correlationStorage->get() !== null) {
            return $this->correlationStorage->resolve($this->generator);
        }

        return $this->correlationStorage->resolve($this->generator);
    }

    public function applyToSpanAttributes(
        SpanInterface $span,
        ?TraceContextInterface $traceContext,
    ): void {
        $correlationId = $this->correlationStorage->get();

        if ($correlationId !== null) {
            $span->setAttribute('correlation.id', $correlationId);
        }

        if ($traceContext instanceof TraceContextInterface) {
            $span->setAttribute('trace.id', $traceContext->getTraceId());
        }
    }
}
