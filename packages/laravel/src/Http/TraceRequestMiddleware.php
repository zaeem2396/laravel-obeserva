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
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class TraceRequestMiddleware
{
    public function __construct(
        private TracerInterface $tracer,
        private ContextStorageInterface $contextStorage,
        private RequestSpanEnricher $enricher,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $incoming = TraceContext::fromPropagationHeaders($request->headers->all());
        $this->contextStorage->set($incoming);

        $span = $this->tracer->startSpan(
            $request->route()?->getName() ?? $request->path(),
            SpanKind::Server,
        );

        $this->enricher->enrichRequest($span, $request);
        $this->enricher->enrichUser($span, $request);
        $span->addEvent('request.received');

        $startedAt = microtime(true);

        try {
            $response = $next($request);

            if (! $response instanceof Response) {
                throw new \RuntimeException('Middleware must return a Response instance.');
            }

            $this->enricher->enrichResponse($span, $response);
            $span->addEvent('response.sent');

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
        }
    }
}
