<?php

declare(strict_types=1);

namespace Obeserva\Core\Tests;

use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use PHPUnit\Framework\TestCase;

final class SpanScopeTest extends TestCase
{
    public function test_scope_ends_span_on_destruct(): void
    {
        $context = new ContextManager;
        $tracer = new Tracer(new AlwaysOnSampler, $context, $context);

        $scope = $tracer->trace('scoped');

        $this->assertInstanceOf(Span::class, $scope->span);
        $this->assertFalse($scope->span->isEnded());

        unset($scope);

        $this->assertCount(1, $tracer->completedSpans());
        $this->assertTrue($tracer->completedSpans()[0]->isEnded());
    }
}
