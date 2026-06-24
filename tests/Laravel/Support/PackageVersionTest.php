<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Support;

use Obeserva\Laravel\Support\PackageVersion;
use PHPUnit\Framework\TestCase;

final class PackageVersionTest extends TestCase
{
    public function test_version_constant_matches_accessor(): void
    {
        $this->assertSame('1.0.0', PackageVersion::VERSION);
        $this->assertSame(PackageVersion::VERSION, PackageVersion::version());
    }
}
