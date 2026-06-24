<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Support;

use InvalidArgumentException;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Support\ConfigValidator;
use Orchestra\Testbench\TestCase;

final class ConfigValidatorTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_valid_configuration_returns_no_errors(): void
    {
        config([
            'obeserva.driver' => 'noop',
            'obeserva.sampling.probability' => 0.5,
            'obeserva.summaries.top_slow_spans' => 5,
        ]);

        $this->assertSame([], $this->app->make(ConfigValidator::class)->validate());
    }

    public function test_invalid_driver_is_reported(): void
    {
        config(['obeserva.driver' => 'invalid']);

        $errors = $this->app->make(ConfigValidator::class)->validate();

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Invalid obeserva.driver', $errors[0]);
    }

    public function test_invalid_sample_rate_is_reported(): void
    {
        config(['obeserva.sampling.probability' => 1.5]);

        $errors = $this->app->make(ConfigValidator::class)->validate();

        $this->assertStringContainsString('probability', $errors[0]);
    }

    public function test_strict_mode_throws_on_invalid_configuration(): void
    {
        config(['obeserva.driver' => 'invalid']);

        $this->expectException(InvalidArgumentException::class);

        $this->app->make(ConfigValidator::class)->assertValid(true);
    }

    public function test_non_strict_mode_does_not_throw(): void
    {
        config(['obeserva.driver' => 'invalid']);

        $this->app->make(ConfigValidator::class)->assertValid(false);

        $this->addToAssertionCount(1);
    }
}
