<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Testing;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Testing\InteractsWithObeserva;
use Obeserva\Testing\TraceSnapshotAssert;
use Orchestra\Testbench\TestCase;

final class InteractsWithObeservaTest extends TestCase
{
    use InteractsWithObeserva;

    protected function getPackageProviders($app): array
    {
        return $this->obeservaPackageProviders();
    }

    protected function defineEnvironment($app): void
    {
        $this->configureObeservaTesting();
    }

    public function test_it_swaps_the_tracer_for_assertions(): void
    {
        $tracer = $this->swapObeservaTracer();

        $parent = $tracer->startSpan('http.request', SpanKind::Server);
        $parent->setAttribute('http.method', 'POST');

        $child = $tracer->startSpan('database.query');
        $child->end();
        $parent->end();

        $tracer->assertSpanCount(2);
        $tracer->assertChildSpanRecorded('http.request', 'database.query');

        TraceSnapshotAssert::assertPropagationIncludesHttpSpan($tracer->spanSnapshots());
    }
}
