<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\OtelDriver\OtelConfig;
use Obeserva\OtelDriver\OtelSemanticConventionMapper;
use Obeserva\OtelDriver\OtelSpanConverter;
use Obeserva\OtelDriver\OtelSpanExporter;
use Obeserva\OtelDriver\OtelSpanKindMapper;
use Obeserva\OtelDriver\OtelSpanNameNormalizer;
use Obeserva\OtelDriver\RecordingOtelExporterClient;
use PHPUnit\Framework\TestCase;

final class OtelSpanExporterTest extends TestCase
{
    public function test_buffers_and_exports_spans_on_flush(): void
    {
        $client = new RecordingOtelExporterClient;
        $config = OtelConfig::fromArray(['enabled' => true, 'service_name' => 'demo']);
        $exporter = new OtelSpanExporter(
            client: $client,
            converter: new OtelSpanConverter(
                config: $config,
                kindMapper: new OtelSpanKindMapper,
                nameNormalizer: new OtelSpanNameNormalizer,
                semanticMapper: new OtelSemanticConventionMapper,
            ),
            config: $config,
        );

        $span = new Span('api.health', SpanKind::Internal, 'trace-1', 'span-1');
        $exporter->onSpanEnded($span);
        $exporter->flush();

        $this->assertCount(1, $client->exportedSpans);
        $this->assertSame('trace-1', $client->exportedSpans[0]['trace_id']);
    }

    public function test_skips_export_when_disabled(): void
    {
        $client = new RecordingOtelExporterClient;
        $config = OtelConfig::fromArray(['enabled' => false]);
        $exporter = new OtelSpanExporter(
            client: $client,
            converter: new OtelSpanConverter(
                config: $config,
                kindMapper: new OtelSpanKindMapper,
                nameNormalizer: new OtelSpanNameNormalizer,
                semanticMapper: new OtelSemanticConventionMapper,
            ),
            config: $config,
        );

        $span = new Span('noop', SpanKind::Internal, 't', 's');
        $exporter->onSpanEnded($span);
        $exporter->flush();

        $this->assertSame([], $client->exportedSpans);
    }
}
