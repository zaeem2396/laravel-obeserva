<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\OtelDriver\OtelConfig;
use Obeserva\OtelDriver\OtelSemanticConventionMapper;
use Obeserva\OtelDriver\OtelSpanConverter;
use Obeserva\OtelDriver\OtelSpanKindMapper;
use Obeserva\OtelDriver\OtelSpanNameNormalizer;
use PHPUnit\Framework\TestCase;

final class OtelSpanConverterTest extends TestCase
{
    public function test_converts_span_to_otel_payload_with_semantic_conventions(): void
    {
        $converter = new OtelSpanConverter(
            config: OtelConfig::fromArray([
                'service_name' => 'obeserva-test',
                'service_version' => '1.0.0',
                'semantic_conventions' => true,
            ]),
            kindMapper: new OtelSpanKindMapper,
            nameNormalizer: new OtelSpanNameNormalizer,
            semanticMapper: new OtelSemanticConventionMapper,
        );

        $span = new Span('users.index', SpanKind::Server, 'trace-1', 'span-1', null, 1000.0);
        $span->setAttribute('http.method', 'GET');
        $span->setAttribute('laravel.route.name', 'users.index');
        $span->end();

        $payload = $converter->convert($span);

        $this->assertSame('trace-1', $payload['trace_id']);
        $this->assertSame('span-1', $payload['span_id']);
        $this->assertSame('GET users.index', $payload['name']);
        $this->assertSame('SPAN_KIND_SERVER', $payload['kind']);
        $this->assertSame('GET', $payload['attributes']['http.request.method']);
        $this->assertSame('obeserva-test', $payload['resource']['service.name']);
        $this->assertSame('1.0.0', $payload['resource']['service.version']);
        $this->assertNotNull($payload['end_time_unix_nano']);
    }
}
