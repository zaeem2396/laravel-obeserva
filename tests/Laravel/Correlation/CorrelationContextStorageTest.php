<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Correlation;

use Obeserva\Laravel\Correlation\CorrelationContextStorage;
use Obeserva\Laravel\Correlation\CorrelationIdGenerator;
use PHPUnit\Framework\TestCase;

final class CorrelationContextStorageTest extends TestCase
{
    public function test_resolves_and_clears_correlation_ids(): void
    {
        $storage = new CorrelationContextStorage;
        $generator = new CorrelationIdGenerator;

        $storage->set('incoming-corr');
        $this->assertSame('incoming-corr', $storage->resolve($generator));

        $storage->clear();
        $generated = $storage->resolve($generator);

        $this->assertNotSame('', $generated);
        $this->assertSame(32, strlen($generated));
    }
}
