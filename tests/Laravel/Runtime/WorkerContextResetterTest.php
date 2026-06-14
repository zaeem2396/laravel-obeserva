<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Runtime;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\ObeservaServiceProvider;
use Obeserva\Laravel\Runtime\WorkerContextResetter;
use Orchestra\Testbench\TestCase;

final class WorkerContextResetterTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_reset_flushes_tracer_and_clears_context(): void
    {
        $context = $this->app->make(ContextManager::class);
        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('worker.job', SpanKind::Consumer);
        $span->end();

        $this->assertSame(1, count($tracer->completedSpans()));

        $this->app->make(WorkerContextResetter::class)->reset();

        $this->assertSame(0, count($tracer->completedSpans()));
        $this->assertNull($context->get());
        $this->assertNull($context->current());
    }
}
