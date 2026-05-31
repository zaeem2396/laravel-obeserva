<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\ScoutDriver\RecordingScoutApmClient;
use Obeserva\ScoutDriver\ScoutConfig;
use Obeserva\ScoutDriver\ScoutContextBridge;
use Obeserva\ScoutDriver\ScoutSpanExporter;
use Obeserva\ScoutDriver\ScoutSpanMapper;
use PHPUnit\Framework\TestCase;

final class ScoutSpanExporterTest extends TestCase
{
    public function test_exports_span_lifecycle_to_scout_client(): void
    {
        $client = new RecordingScoutApmClient(enabled: true);
        $config = ScoutConfig::fromArray([
            'enabled' => true,
            'application_name' => 'demo',
            'monitoring_enabled' => true,
        ]);

        $exporter = new ScoutSpanExporter(
            client: $client,
            mapper: new ScoutSpanMapper,
            contextBridge: new ScoutContextBridge($client, $config),
            config: $config,
        );

        $span = new Span('users.index', SpanKind::Server, 'trace-1', 'span-1');
        $exporter->onSpanStarted($span);
        $span->setAttribute('http.status_code', 200);
        $exporter->onSpanEnded($span);
        $exporter->flush();

        $startActions = array_values(array_filter(
            $client->actions,
            static fn (array $action): bool => $action['type'] === 'startSpan',
        ));

        $this->assertNotEmpty($startActions);
        $this->assertSame('HTTP/users.index', $startActions[0]['operation']);
        $this->assertContains(['type' => 'stopSpan'], $client->actions);
        $this->assertContains(['type' => 'send'], $client->actions);
    }

    public function test_skips_export_when_disabled(): void
    {
        $client = new RecordingScoutApmClient(enabled: true);
        $config = ScoutConfig::fromArray(['enabled' => false]);
        $exporter = new ScoutSpanExporter(
            client: $client,
            mapper: new ScoutSpanMapper,
            contextBridge: new ScoutContextBridge($client, $config),
            config: $config,
        );

        $span = new Span('noop', SpanKind::Internal, 't', 's');
        $exporter->onSpanStarted($span);
        $exporter->onSpanEnded($span);
        $exporter->flush();

        $this->assertSame([], $client->actions);
    }
}
