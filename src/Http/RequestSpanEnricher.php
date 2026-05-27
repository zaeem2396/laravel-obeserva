<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Obeserva\Contracts\Span\SpanInterface;
use Symfony\Component\HttpFoundation\Response;

final class RequestSpanEnricher
{
    public function enrichRequest(SpanInterface $span, Request $request): void
    {
        $span->setAttribute('http.method', $request->method());
        $span->setAttribute('http.route', $request->route()?->uri() ?? $request->path());
        $span->setAttribute('http.url', $request->fullUrl());
        $span->setAttribute('http.scheme', $request->getScheme());
        $span->setAttribute('http.host', $request->getHost());
        $span->setAttribute('http.client_ip', $request->ip() ?? '');
        $span->setAttribute('http.user_agent', $request->userAgent() ?? '');

        if ($request->route()?->getName() !== null) {
            $span->setAttribute('laravel.route.name', (string) $request->route()->getName());
        }
    }

    public function enrichRoute(SpanInterface $span, Route $route): void
    {
        if ($route->getName() !== null) {
            $span->setAttribute('laravel.route.name', (string) $route->getName());
        }

        $action = $route->getActionName();

        if ($action !== 'Closure' && $action !== '') {
            $span->setAttribute('laravel.route.action', $action);
        }

        $middleware = [];

        foreach ($route->gatherMiddleware() as $name) {
            if (is_string($name)) {
                $middleware[] = $name;
            }
        }

        if ($middleware !== []) {
            $span->setAttribute('laravel.middleware.stack', implode(',', $middleware));
        }
    }

    public function enrichResponse(SpanInterface $span, Response $response): void
    {
        $span->setAttribute('http.status_code', $response->getStatusCode());

        if ($response->headers->has('Content-Type')) {
            $span->setAttribute('http.response.content_type', (string) $response->headers->get('Content-Type'));
        }

        if ($response->headers->has('Content-Length')) {
            $span->setAttribute('http.response.content_length', (string) $response->headers->get('Content-Length'));
        }
    }

    public function enrichException(SpanInterface $span, \Throwable $exception): void
    {
        $span->setAttribute('exception.type', $exception::class);
        $span->setAttribute('exception.message', $exception->getMessage());
        $span->addEvent('exception', [
            'exception.type' => $exception::class,
            'exception.message' => $exception->getMessage(),
        ]);
    }

    public function enrichUser(SpanInterface $span, Request $request): void
    {
        $user = $request->user();

        if ($user === null) {
            return;
        }

        $identifier = $user->getAuthIdentifier();
        $span->setAttribute('user.id', is_scalar($identifier) ? (string) $identifier : '');
        $span->setAttribute('user.authenticatable', $user::class);
    }
}
