<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Illuminate\Support\Facades\Route;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\DeveloperExperience\Telescope\PublishTraceToTelescope;
use Obeserva\DeveloperExperience\Telescope\RecordingTelescopePublisher;
use Obeserva\DeveloperExperience\Telescope\TelescopePublisherInterface;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class DeveloperExperienceIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_debug_toolbar_injects_html_when_enabled(): void
    {
        config([
            'obeserva.development.debug_toolbar.enabled' => true,
            'obeserva.http.middleware_enabled' => true,
        ]);

        Route::get('/obeserva-debug-test', fn () => response('<html><body>ok</body></html>', 200, [
            'Content-Type' => 'text/html',
        ]));

        $response = $this->get('/obeserva-debug-test');

        $response->assertOk();
        $this->assertStringContainsString('obeserva-debug-toolbar', (string) $response->getContent());
    }

    public function test_telescope_publisher_receives_spans_on_terminate(): void
    {
        $publisher = new RecordingTelescopePublisher;
        $this->app->instance(TelescopePublisherInterface::class, $publisher);

        config([
            'obeserva.development.telescope.enabled' => true,
            'obeserva.development.debug_toolbar.enabled' => true,
            'obeserva.http.middleware_enabled' => true,
        ]);

        Route::get('/obeserva-telescope-test', fn () => 'done');

        $this->get('/obeserva-telescope-test');

        $this->app->make(PublishTraceToTelescope::class)->handle();

        $this->assertNotEmpty($publisher->entries);
        $this->assertSame('obeserva-trace', $publisher->entries[0]['type']);
    }

    public function test_lifecycle_resolver_collects_snapshots_when_development_enabled(): void
    {
        config([
            'obeserva.development.debug_toolbar.enabled' => true,
        ]);

        $registry = $this->app->make(TraceSnapshotRegistry::class);
        $tracer = $this->app->make(TracerInterface::class);

        $span = $tracer->startSpan('manual.work');
        $span->end();

        $this->assertGreaterThanOrEqual(1, $registry->count());
    }
}
