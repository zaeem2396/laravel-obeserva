<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Database;

use Obeserva\Laravel\Database\QuerySanitizer;
use PHPUnit\Framework\TestCase;

final class QuerySanitizerTest extends TestCase
{
    public function test_replaces_bindings_in_placeholders(): void
    {
        $sanitizer = new QuerySanitizer;

        $sql = $sanitizer->sanitize('select * from users where id = ? and active = ?', [1, true]);

        $this->assertStringContainsString('id = 1', $sql);
        $this->assertStringContainsString('active = 1', $sql);
    }

    public function test_truncates_long_statements(): void
    {
        $sanitizer = new QuerySanitizer;
        $long = str_repeat('a', 5000);

        $sql = $sanitizer->sanitize($long);

        $this->assertSame(4099, strlen($sql));
        $this->assertStringEndsWith('...', $sql);
    }
}
