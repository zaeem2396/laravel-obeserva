<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Correlation\IncomingCorrelationResolver;
use Obeserva\Laravel\Correlation\OutgoingCorrelationHeaders;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class TraceRequestMiddleware
{
    public function __construct(
        private TracerInterface $tracer,
        private ContextStorageInterface $contextStorage,
        private RequestSpanEnricher $enricher,
        private IncomingCorrelationResolver $correlationResolver,
        private OutgoingCorrelationHeaders $outgoingCorrelationHeaders,
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $incoming = TraceContext::fromPropagationHeaders($request->headers->all());
        $this->contextStorage->set($incoming);

        if ((bool) config('obeserva.correlation.enabled', true)) {
            $header = config('obeserva.correlation.header', 'X-Correlation-ID');
            $this->correlationResolver->resolveFromRequest(
                $request,
                is_string($header) ? $header : 'X-Correlation-ID',
            );
        }

        $span = $this->tracer->startSpan(
            $request->route()?->getName() ?? $request->path(),
            SpanKind::Server,
        );

        $this->enricher->enrichRequest($span, $request);
        $this->enricher->enrichUser($span, $request);
        $this->correlationResolver->applyToSpanAttributes($span, $incoming);
        $span->addEvent('request.received');

        $startedAt = microtime(true);

        try {
            $response = $next($request);

            if (! $response instanceof Response) {
                throw new \RuntimeException('Middleware must return a Response instance.');
            }

            $this->enricher->enrichResponse($span, $response);
            $span->addEvent('response.sent');

            if ((bool) config('obeserva.correlation.enabled', true) && (bool) config('obeserva.correlation.propagate_outbound', true)) {
                $header = config('obeserva.correlation.header', 'X-Correlation-ID');
                $this->outgoingCorrelationHeaders->apply(
                    $response,
                    is_string($header) ? $header : 'X-Correlation-ID',
                );
            }

            return $response;
        } catch (Throwable $exception) {
            $this->enricher->enrichException($span, $exception);

            throw $exception;
        } finally {
            $span->setAttribute('http.duration_ms', (microtime(true) - $startedAt) * 1000);

            if (! $span->isEnded()) {
                $span->end();
            }

            if ($this->contextStorage instanceof ContextManager) {
                $this->contextStorage->clear();
            } else {
                $this->contextStorage->set(null);
            }

            $this->correlationStorage->clear();
        }
    }
}
