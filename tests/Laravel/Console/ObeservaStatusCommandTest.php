<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Console;

use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class ObeservaStatusCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_status_command_outputs_package_version(): void
    {
        $this->artisan('obeserva:status')
            ->expectsOutputToContain('1.0.0')
            ->assertSuccessful();
    }

    public function test_status_command_json_output(): void
    {
        $this->artisan('obeserva:status', ['--json' => true])
            ->expectsOutputToContain('"package_version": "1.0.0"')
            ->assertSuccessful();
    }
}
