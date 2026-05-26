<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records a child span for a named middleware pipeline segment.
 *
 * Register as `obeserva.timing:segment` (alias provided by ObeservaServiceProvider).
 */
final readonly class TraceMiddlewareTiming
{
    public function __construct(
        private TracerInterface $tracer,
    ) {}

    public function handle(Request $request, Closure $next, string $name = 'pipeline'): Response
    {
        $span = $this->tracer->startSpan(
            'middleware.'.$name,
            SpanKind::Internal,
        );

        $startedAt = microtime(true);

        try {
            $response = $next($request);

            if (! $response instanceof Response) {
                throw new \RuntimeException('Middleware must return a Response instance.');
            }

            return $response;
        } finally {
            $span->setAttribute('middleware.name', $name);
            $span->setAttribute('middleware.duration_ms', (microtime(true) - $startedAt) * 1000);
            $span->end();
        }
    }
}
