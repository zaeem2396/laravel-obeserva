<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests\Context;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use PHPUnit\Framework\TestCase;

final class ContextManagerDepthLimitTest extends TestCase
{
    public function test_ends_oldest_span_when_active_depth_exceeded(): void
    {
        $manager = new ContextManager;
        $manager->configureMaxActiveSpanDepth(2);

        $first = new Span('first', SpanKind::Internal, 'trace', 'span-1');
        $second = new Span('second', SpanKind::Internal, 'trace', 'span-2');
        $third = new Span('third', SpanKind::Internal, 'trace', 'span-3');

        $manager->push($first);
        $manager->push($second);
        $manager->push($third);

        $this->assertTrue($first->isEnded());
        $this->assertFalse($second->isEnded());
        $this->assertSame($third, $manager->current());
    }
}
