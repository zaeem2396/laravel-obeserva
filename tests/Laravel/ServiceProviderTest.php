<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_tracer_is_bound_when_enabled(): void
    {
        config(['obeserva.enabled' => true]);

        $app = $this->app;
        $this->assertNotNull($app);
        $this->assertInstanceOf(TracerInterface::class, $app->make(TracerInterface::class));
    }

    public function test_config_is_merged(): void
    {
        $this->assertTrue(config('obeserva.enabled'));
        $this->assertSame('noop', config('obeserva.driver'));
    }
}
