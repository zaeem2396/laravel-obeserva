<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $span->setAttribute('http.url', $request->fullUrl());

        if ($request->route()?->getName() !== null) {
            $span->setAttribute('laravel.route.name', (string) $request->route()->getName());
        }

        if (Auth::check()) {
            $span->setAttribute('user.id', (string) Auth::id());
        }

        $startedAt = microtime(true);

        try {
            $response = $next($request);

            if (! $response instanceof Response) {
                throw new \RuntimeException('Middleware must return a Response instance.');
            }

            $span->setAttribute('http.status_code', $response->getStatusCode());

            return $response;
        } catch (Throwable $exception) {
            $span->setAttribute('exception.type', $exception::class);
            $span->setAttribute('exception.message', $exception->getMessage());

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
