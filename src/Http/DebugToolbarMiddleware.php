<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Obeserva\DeveloperExperience\DebugToolbar\DebugToolbarDataBuilder;
use Obeserva\DeveloperExperience\DebugToolbar\DebugToolbarRenderer;
use Symfony\Component\HttpFoundation\Response;

final readonly class DebugToolbarMiddleware
{
    public function __construct(
        private DebugToolbarDataBuilder $dataBuilder,
        private DebugToolbarRenderer $renderer,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof Response) {
            throw new \RuntimeException('Middleware must return a Response instance.');
        }

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $payload = $this->dataBuilder->build();
        $toolbar = $this->renderer->renderHtml($payload);
        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        if (str_contains($content, '</body>')) {
            $content = str_replace('</body>', $toolbar.'</body>', $content);
        } else {
            $content .= $toolbar;
        }

        $response->setContent($content);

        return $response;
    }

    private function shouldInject(Request $request, Response $response): bool
    {
        if (! config('obeserva.development.debug_toolbar.enabled', false)) {
            return false;
        }

        if ($request->expectsJson() || $request->is('telescope*')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html')
            || $contentType === ''
            || str_contains($contentType, 'text/');
    }
}
