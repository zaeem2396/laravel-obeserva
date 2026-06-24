<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Support;

use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Support\RuntimeDiagnosticsBuilder;
use Orchestra\Testbench\TestCase;

final class RuntimeDiagnosticsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_builder_produces_package_diagnostics(): void
    {
        config([
            'obeserva.driver' => 'noop',
            'obeserva.sampling.probability' => 1.0,
            'obeserva.summaries.enabled' => true,
        ]);

        $diagnostics = $this->app->make(RuntimeDiagnosticsBuilder::class)->build();

        $this->assertSame('1.0.0', $diagnostics->packageVersion);
        $this->assertSame('noop', $diagnostics->driver);
        $this->assertTrue($diagnostics->enabled);
        $this->assertSame('http', $diagnostics->workerRuntime);
        $this->assertTrue($diagnostics->features['summaries']);

        $array = $diagnostics->toArray();
        $this->assertSame('1.0.0', $array['package_version']);
        $this->assertArrayHasKey('runtime', $array);
    }
}
