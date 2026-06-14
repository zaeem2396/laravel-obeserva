<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;

final class OtelSpanNameNormalizer
{
    public function normalize(SpanInterface $span): string
    {
        $attributes = $span->getAttributes();

        if ($span->getKind() === SpanKind::Server) {
            $method = is_string($attributes['http.method'] ?? null) ? $attributes['http.method'] : 'HTTP';
            $route = is_string($attributes['laravel.route.name'] ?? null)
                ? $attributes['laravel.route.name']
                : (is_string($attributes['http.route'] ?? null) ? $attributes['http.route'] : $span->getName());

            return $method.' '.$route;
        }

        if ($span->getKind() === SpanKind::Consumer) {
            $job = is_string($attributes['queue.job'] ?? null) ? $attributes['queue.job'] : $span->getName();

            return 'process '.$job;
        }

        return $span->getName();
    }
}
