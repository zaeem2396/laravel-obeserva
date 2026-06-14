<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Obeserva\Core\Export\CompositeSpanLifecycleExporter;
use Obeserva\Laravel\Driver\LifecycleExporterResolver;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class LifecycleExporterResolverTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_returns_composite_exporter_when_development_enabled(): void
    {
        config(['obeserva.development.debug_toolbar.enabled' => true]);

        $exporter = $this->app->make(LifecycleExporterResolver::class)->resolve();

        $this->assertInstanceOf(CompositeSpanLifecycleExporter::class, $exporter);
    }

    public function test_returns_primary_exporter_when_development_disabled(): void
    {
        config([
            'obeserva.development.debug_toolbar.enabled' => false,
            'obeserva.development.telescope.enabled' => false,
            'obeserva.driver' => 'noop',
        ]);

        $exporter = $this->app->make(LifecycleExporterResolver::class)->resolve();

        $this->assertNotInstanceOf(CompositeSpanLifecycleExporter::class, $exporter);
    }
}
