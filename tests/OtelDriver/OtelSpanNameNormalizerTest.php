<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\OtelDriver\OtelSpanNameNormalizer;
use PHPUnit\Framework\TestCase;

final class OtelSpanNameNormalizerTest extends TestCase
{
    public function test_normalizes_http_server_span_name(): void
    {
        $normalizer = new OtelSpanNameNormalizer;
        $span = new Span('users.index', SpanKind::Server, 't', 's');
        $span->setAttribute('http.method', 'GET');
        $span->setAttribute('laravel.route.name', 'users.index');

        $this->assertSame('GET users.index', $normalizer->normalize($span));
    }

    public function test_normalizes_queue_consumer_span_name(): void
    {
        $normalizer = new OtelSpanNameNormalizer;
        $span = new Span('queue.process', SpanKind::Consumer, 't', 's');
        $span->setAttribute('queue.job', 'App\\Jobs\\SendEmail');

        $this->assertSame('process App\\Jobs\\SendEmail', $normalizer->normalize($span));
    }
}
