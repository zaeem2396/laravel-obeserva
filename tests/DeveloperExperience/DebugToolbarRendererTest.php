<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\DebugToolbar\DebugToolbarDataBuilder;
use Obeserva\DeveloperExperience\DebugToolbar\DebugToolbarRenderer;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use Obeserva\DeveloperExperience\TraceTreeBuilder;
use PHPUnit\Framework\TestCase;

final class DebugToolbarRendererTest extends TestCase
{
    public function test_renders_html_with_span_summary(): void
    {
        $registry = new TraceSnapshotRegistry;
        $factory = new SpanSnapshotFactory;

        $span = new Span('http.request', SpanKind::Server, 'trace', 'span');
        $span->setAttribute('http.method', 'GET');
        $span->end();
        $registry->record($factory->fromSpan($span));

        $payload = (new DebugToolbarDataBuilder(
            $registry,
            new TraceTreeBuilder,
            new PropagationFlowInspector,
        ))->build();

        $html = (new DebugToolbarRenderer)->renderHtml($payload);

        $this->assertStringContainsString('id="obeserva-debug-toolbar"', $html);
        $this->assertStringContainsString('http.request', $html);
        $this->assertStringContainsString('1 spans', $html);
    }
}
