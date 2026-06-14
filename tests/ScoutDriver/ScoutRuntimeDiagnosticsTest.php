<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\ScoutDriver\ScoutRuntimeDiagnostics;
use PHPUnit\Framework\TestCase;

final class ScoutRuntimeDiagnosticsTest extends TestCase
{
    public function test_to_tags_includes_php_laravel_and_env(): void
    {
        $diagnostics = new ScoutRuntimeDiagnostics('8.3.6', '12.19.0', 'staging', true);

        $tags = $diagnostics->toTags();

        $this->assertSame('8.3.6', $tags['scout.php.version']);
        $this->assertSame('12.19.0', $tags['scout.laravel.version']);
        $this->assertSame('staging', $tags['scout.app.env']);
        $this->assertSame('true', $tags['scout.app.debug']);
    }

    public function test_omits_laravel_version_when_empty(): void
    {
        $diagnostics = new ScoutRuntimeDiagnostics('8.3.6', '', 'unknown', false);

        $this->assertArrayNotHasKey('scout.laravel.version', $diagnostics->toTags());
    }
}
