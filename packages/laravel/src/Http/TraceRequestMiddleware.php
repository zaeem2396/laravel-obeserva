<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContext;
use Symfony\Component\HttpFoundation\Response;

final readonly class TraceRequestMiddleware
{
    public function __construct(
        private TracerInterface $tracer,
        private ContextStorageInterface $contextStorage,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $incoming = TraceContext::fromPropagationHeaders($request->headers->all());
        $this->contextStorage->set($incoming);

        $span = $this->tracer->startSpan(
            $request->route()?->getName() ?? $request->path(),
            SpanKind::Server,
        );

        $span->setAttribute('http.method', $request->method());
        $span->setAttribute('http.route', $request->route()?->uri() ?? $request->path());

        try {
            $response = $next($request);

            if (! $response instanceof Response) {
                throw new \RuntimeException('Middleware must return a Response instance.');
            }

            $span->setAttribute('http.status_code', $response->getStatusCode());

            return $response;
        } finally {
            $span->end();
            $this->contextStorage->set(null);
        }
    }
}
