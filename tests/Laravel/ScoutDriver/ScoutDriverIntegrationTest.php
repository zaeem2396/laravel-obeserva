<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\ScoutDriver;

use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\ScoutDriver\RecordingScoutApmClient;
use Obeserva\ScoutDriver\ScoutApmClientInterface;
use Orchestra\Testbench\TestCase;

final class ScoutDriverIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.driver', 'scout');
        $app['config']->set('obeserva.scout.enabled', true);
        $app['config']->set('obeserva.scout.monitoring_enabled', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_scout_driver_exports_spans_on_flush(): void
    {
        $client = new RecordingScoutApmClient(enabled: true);
        $this->app->instance(ScoutApmClientInterface::class, $client);
        $this->app->forgetInstance(SpanLifecycleExporterInterface::class);
        $this->app->forgetInstance(TracerInterface::class);

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('api.health');
        $span->setAttribute('http.method', 'GET');
        $span->end();

        $tracer->flush();

        $this->assertNotEmpty($client->actions);
        $this->assertContains(['type' => 'startSpan', 'operation' => 'Custom/api.health'], $client->actions);
        $this->assertContains(['type' => 'send'], $client->actions);
    }
}
