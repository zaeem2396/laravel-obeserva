<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests\Flush;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Flush\TracerFlushGuard;
use PHPUnit\Framework\TestCase;

final class TracerFlushGuardTest extends TestCase
{
    public function test_swallows_exporter_exceptions_when_guarding(): void
    {
        $guard = new TracerFlushGuard(new ThrowingTracer, true);

        $guard->flush();

        $this->addToAssertionCount(1);
    }

    public function test_rethrows_when_guard_disabled(): void
    {
        $guard = new TracerFlushGuard(new ThrowingTracer, false);

        $this->expectException(\RuntimeException::class);
        $guard->flush();
    }
}

final class ThrowingTracer implements TracerInterface
{
    public function startSpan(string $name, SpanKind $kind = SpanKind::Internal): SpanInterface
    {
        throw new \RuntimeException('not used');
    }

    public function flush(): void
    {
        throw new \RuntimeException('export failed');
    }
}
