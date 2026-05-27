<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Http\RequestSpanEnricher;
use Obeserva\Laravel\Listeners\ReportExceptionListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;
use RuntimeException;

final class ReportExceptionListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    public function test_reports_to_active_span_when_present(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $context = $app->make(ContextManager::class);
        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $span = $tracer->startSpan('request');
        $this->assertInstanceOf(Span::class, $span);

        $listener = new ReportExceptionListener($tracer, $context, new RequestSpanEnricher);
        $listener->report(new RuntimeException('disk full'));

        $attributes = $span->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame(RuntimeException::class, $attributes['exception.type']);
        $this->assertSame('disk full', $attributes['exception.message']);
    }

    public function test_creates_exception_span_without_active_request(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);
        $context = $app->make(ContextManager::class);

        $listener = new ReportExceptionListener($tracer, $context, new RequestSpanEnricher);
        $listener->report(new RuntimeException('worker failed'));

        $completed = $tracer->completedSpans();
        $this->assertCount(1, $completed);
        $this->assertSame('exception', $completed[0]->getName());
    }
}
