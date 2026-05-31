<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\ScoutDriver\RecordingScoutApmClient;
use Obeserva\ScoutDriver\ScoutConfig;
use Obeserva\ScoutDriver\ScoutContextBridge;
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
}
