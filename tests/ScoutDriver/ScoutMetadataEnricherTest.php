<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\ScoutDriver\ScoutConfig;
use Obeserva\ScoutDriver\ScoutMetadataEnricher;
use Obeserva\ScoutDriver\ScoutRuntimeDiagnostics;
use Obeserva\ScoutDriver\ScoutSpanMetadataMapper;
use PHPUnit\Framework\TestCase;

final class ScoutMetadataEnricherTest extends TestCase
{
    public function test_runtime_tags_include_diagnostics_deployment_and_tenant(): void
    {
        $enricher = new ScoutMetadataEnricher(
            config: ScoutConfig::fromArray([
                'deployment_version' => '2026.05.28',
                'tenant_id' => 'acme',
            ]),
            mapper: new ScoutSpanMetadataMapper,
            diagnostics: new ScoutRuntimeDiagnostics('8.3.6', '12.0.0', 'testing', false),
        );

        $tags = $enricher->runtimeTags();

        $this->assertSame('2026.05.28', $tags['scout.deployment.version']);
        $this->assertSame('acme', $tags['scout.tenant.id']);
        $this->assertSame('1.0.0', $tags['scout.obeserva.version']);
        $this->assertSame('8.3.6', $tags['scout.php.version']);
        $this->assertSame('12.0.0', $tags['scout.laravel.version']);
        $this->assertSame('false', $tags['scout.app.debug']);
    }

    public function test_span_tags_delegate_to_mapper(): void
    {
        $enricher = new ScoutMetadataEnricher(
            config: ScoutConfig::fromArray(['metadata_enabled' => true]),
            mapper: new ScoutSpanMetadataMapper,
            diagnostics: new ScoutRuntimeDiagnostics('8.3.6', '', 'local', true),
        );

        $span = new Span('queue.process', SpanKind::Consumer, 't', 's');
        $span->setAttribute('queue.name', 'default');

        $this->assertSame(['scout.queue.name' => 'default'], $enricher->spanTags($span));
    }

    public function test_returns_empty_when_metadata_disabled(): void
    {
        $enricher = new ScoutMetadataEnricher(
            config: ScoutConfig::fromArray(['metadata_enabled' => false]),
            mapper: new ScoutSpanMetadataMapper,
            diagnostics: new ScoutRuntimeDiagnostics('8.3.6', '12.0.0', 'local', true),
        );

        $span = new Span('work', SpanKind::Internal, 't', 's');
        $span->setAttribute('queue.name', 'default');

        $this->assertSame([], $enricher->runtimeTags());
        $this->assertSame([], $enricher->spanTags($span));
    }
}
