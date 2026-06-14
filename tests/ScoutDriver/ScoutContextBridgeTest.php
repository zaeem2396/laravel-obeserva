<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\ScoutDriver\RecordingScoutApmClient;
use Obeserva\ScoutDriver\ScoutConfig;
use Obeserva\ScoutDriver\ScoutContextBridge;
use Obeserva\ScoutDriver\ScoutMetadataEnricher;
use Obeserva\ScoutDriver\ScoutRuntimeDiagnostics;
use Obeserva\ScoutDriver\ScoutSpanMetadataMapper;
use PHPUnit\Framework\TestCase;

final class ScoutContextBridgeTest extends TestCase
{
    public function test_applies_default_tags_and_span_context(): void
    {
        $client = new RecordingScoutApmClient;
        $bridge = new ScoutContextBridge(
            client: $client,
            config: ScoutConfig::fromArray([
                'enabled' => true,
                'application_name' => 'obeserva-test',
                'default_tags' => ['team' => 'platform'],
            ]),
        );

        $bridge->applyDefaultTags();

        $span = new Span('users.index', SpanKind::Server, 'trace-1', 'span-1');
        $span->setAttribute('http.method', 'GET');
        $bridge->bridgeSpanContext($span);
        $bridge->bridgeSpanAttributes($span);

        $this->assertSame(
            [
                ['type' => 'tagRequest', 'tag' => 'team', 'value' => 'platform'],
                ['type' => 'tagRequest', 'tag' => 'obeserva.application', 'value' => 'obeserva-test'],
                ['type' => 'addContext', 'tag' => 'trace.id', 'value' => 'trace-1'],
                ['type' => 'addContext', 'tag' => 'span.id', 'value' => 'span-1'],
                ['type' => 'addContext', 'tag' => 'http.method', 'value' => 'GET'],
                ['type' => 'tagRequest', 'tag' => 'http.method', 'value' => 'GET'],
            ],
            $client->actions,
        );
    }

    public function test_applies_runtime_and_span_metadata_tags(): void
    {
        $client = new RecordingScoutApmClient;
        $config = ScoutConfig::fromArray([
            'deployment_version' => 'v1.2.3',
            'tenant_id' => 'tenant-a',
        ]);
        $bridge = new ScoutContextBridge(
            client: $client,
            config: $config,
            metadataEnricher: new ScoutMetadataEnricher(
                config: $config,
                mapper: new ScoutSpanMetadataMapper,
                diagnostics: new ScoutRuntimeDiagnostics('8.3.6', '12.0.0', 'testing', false),
            ),
        );

        $bridge->applyDefaultTags();

        $span = new Span('users.index', SpanKind::Server, 'trace-1', 'span-1');
        $span->setAttribute('laravel.route.name', 'users.index');
        $span->setAttribute('http.method', 'GET');
        $bridge->bridgeSpanContext($span);
        $bridge->bridgeSpanAttributes($span);

        $this->assertContains(['type' => 'tagRequest', 'tag' => 'scout.deployment.version', 'value' => 'v1.2.3'], $client->actions);
        $this->assertContains(['type' => 'tagRequest', 'tag' => 'scout.tenant.id', 'value' => 'tenant-a'], $client->actions);
        $this->assertContains(['type' => 'addContext', 'tag' => 'scout.route.name', 'value' => 'users.index'], $client->actions);
        $this->assertContains(['type' => 'tagRequest', 'tag' => 'scout.route.name', 'value' => 'users.index'], $client->actions);
    }
}
