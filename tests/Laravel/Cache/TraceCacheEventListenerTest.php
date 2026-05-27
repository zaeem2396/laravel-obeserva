<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Cache;

use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\TraceCacheEventListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceCacheEventListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.cache.enabled', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_cache_hit_records_span(): void
    {
        $listener = $this->app->make(TraceCacheEventListener::class);
        $listener->handle(new CacheHit('array', 'foo', 'bar'));

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $spans = $tracer->completedSpans();
        $this->assertCount(1, $spans);
        $this->assertSame('cache.get', $spans[0]->getName());

        $attributes = $spans[0]->toArray()['attributes'];
        $this->assertSame('array', $attributes['cache.store']);
        $this->assertSame('foo', $attributes['cache.key']);
        $this->assertTrue($attributes['cache.hit']);
    }

    public function test_cache_miss_records_span(): void
    {
        $listener = $this->app->make(TraceCacheEventListener::class);
        $listener->handle(new CacheMissed('array', 'missing'));

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $spans = $tracer->completedSpans();
        $this->assertCount(1, $spans);
        $this->assertSame('cache.miss', $spans[0]->getName());

        $attributes = $spans[0]->toArray()['attributes'];
        $this->assertSame('array', $attributes['cache.store']);
        $this->assertSame('missing', $attributes['cache.key']);
        $this->assertFalse($attributes['cache.hit']);
    }

    public function test_cache_put_records_span(): void
    {
        $listener = $this->app->make(TraceCacheEventListener::class);
        $listener->handle(new KeyWritten('array', 'ttl', 'v', 60));

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $spans = $tracer->completedSpans();
        $this->assertCount(1, $spans);
        $this->assertSame('cache.put', $spans[0]->getName());

        $attributes = $spans[0]->toArray()['attributes'];
        $this->assertSame(60, $attributes['cache.ttl_seconds']);
    }

    public function test_cache_forget_records_span(): void
    {
        $listener = $this->app->make(TraceCacheEventListener::class);
        $listener->handle(new KeyForgotten('array', 'gone'));

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $spans = $tracer->completedSpans();
        $this->assertCount(1, $spans);
        $this->assertSame('cache.forget', $spans[0]->getName());
    }
}

