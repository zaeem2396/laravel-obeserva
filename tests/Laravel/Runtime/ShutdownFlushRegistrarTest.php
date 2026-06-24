<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Runtime\ShutdownFlushRegistrar;
use Orchestra\Testbench\TestCase;

final class ShutdownFlushRegistrarTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.flush.on_shutdown', true);
    }

    public function test_register_is_idempotent(): void
    {
        $registrar = $this->app->make(ShutdownFlushRegistrar::class);

        $registrar->register($this->app);
        $registrar->register($this->app);

        $this->addToAssertionCount(1);
    }
}
