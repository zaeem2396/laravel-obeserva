<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Analysis;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryRegistry;
use Obeserva\DeveloperExperience\SpanSnapshotCollector;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use Obeserva\Laravel\Listeners\BuildTraceSummaryOnTerminate;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Testing\TraceSummaryAssert;
use Orchestra\Testbench\TestCase;

final class BuildTraceSummaryOnTerminateTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.summaries.enabled', true);
        $app['config']->set('obeserva.causation.enabled', true);
        $app['config']->set('obeserva.causation.slow_request_threshold_ms', 500);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_builds_and_stores_trace_summary_on_terminate(): void
    {
        $registry = $this->app->make(TraceSnapshotRegistry::class);
        $collector = new SpanSnapshotCollector($registry, new SpanSnapshotFactory);

        $http = new Span('GET /slow', SpanKind::Server, 'trace', 'http');
        $http->setAttribute('http.method', 'GET');
        $http->setAttribute('http.duration_ms', 1200);
        $http->end();
        $collector->onSpanEnded($http);

        $db = new Span('db.select', SpanKind::Client, 'trace', 'db', 'http');
        $db->end();
        $collector->onSpanEnded($db);

        $this->app->make(BuildTraceSummaryOnTerminate::class)->handle();

        $summary = $this->app->make(TraceSummaryRegistry::class)->latest();

        $this->assertNotNull($summary);
        TraceSummaryAssert::assertSlowRequest($summary);
        TraceSummaryAssert::assertHasCausationGraph($summary);
        $this->assertSame(2, $summary->spanCount);
    }
}
