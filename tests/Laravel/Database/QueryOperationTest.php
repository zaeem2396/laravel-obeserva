<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Database;

use Obeserva\Laravel\Database\QueryOperation;
use PHPUnit\Framework\TestCase;

final class QueryOperationTest extends TestCase
{
    public function test_detects_sql_operation(): void
    {
        $this->assertSame('select', QueryOperation::fromSql('SELECT * FROM users'));
        $this->assertSame('insert', QueryOperation::fromSql('  insert into users values (1)'));
        $this->assertSame('other', QueryOperation::fromSql('SHOW TABLES'));
    }
}
