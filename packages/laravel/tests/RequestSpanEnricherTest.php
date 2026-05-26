<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Http\Request;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Http\RequestSpanEnricher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class RequestSpanEnricherTest extends TestCase
{
    private RequestSpanEnricher $enricher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enricher = new RequestSpanEnricher;
    }

    public function test_enrich_response_sets_status_and_headers(): void
    {
        $span = new Span('http', SpanKind::Server, 'trace', 'span');
        $response = new Response('body', 201, ['Content-Type' => 'application/json', 'Content-Length' => '4']);

        $this->enricher->enrichResponse($span, $response);

        $attributes = $span->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame(201, $attributes['http.status_code']);
        $this->assertSame('application/json', $attributes['http.response.content_type']);
        $this->assertSame('4', $attributes['http.response.content_length']);
    }

    public function test_enrich_request_sets_client_metadata(): void
    {
        $span = new Span('http', SpanKind::Server, 'trace', 'span');
        $request = Request::create('https://example.com/posts', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'ObeservaTest/1.0',
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        $this->enricher->enrichRequest($span, $request);

        $attributes = $span->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame('GET', $attributes['http.method']);
        $this->assertSame('ObeservaTest/1.0', $attributes['http.user_agent']);
    }
}
