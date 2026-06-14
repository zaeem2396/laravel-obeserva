<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\OtelDriver;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\OtelDriver\OtelExporterClientInterface;
use Obeserva\OtelDriver\RecordingOtelExporterClient;
use Orchestra\Testbench\TestCase;

final class OtelDriverIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.driver', 'otel');
        $app['config']->set('obeserva.otel.enabled', true);
        $app['config']->set('obeserva.otel.service_name', 'integration-test');
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_otel_driver_exports_spans_on_flush(): void
    {
        $client = new RecordingOtelExporterClient;
        $this->app->instance(OtelExporterClientInterface::class, $client);
        $this->app->forgetInstance(SpanLifecycleExporterInterface::class);
        $this->app->forgetInstance(TracerInterface::class);

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('api.health', SpanKind::Server);
        $span->setAttribute('http.method', 'GET');
        $span->setAttribute('laravel.route.name', 'api.health');
        $span->end();

        $tracer->flush();

        $this->assertCount(1, $client->exportedSpans);
        $this->assertSame('GET api.health', $client->exportedSpans[0]['name']);
        $this->assertSame('SPAN_KIND_SERVER', $client->exportedSpans[0]['kind']);
        $this->assertSame('integration-test', $client->exportedSpans[0]['resource']['service.name']);
    }
}
