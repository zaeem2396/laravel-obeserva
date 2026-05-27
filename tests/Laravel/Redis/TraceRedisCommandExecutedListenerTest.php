<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Redis;

use Illuminate\Redis\Events\CommandExecuted;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\TraceRedisCommandExecutedListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceRedisCommandExecutedListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.redis.command_tracing', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_redis_command_records_span(): void
    {
        $listener = $this->app->make(TraceRedisCommandExecutedListener::class);

        $fakeConnection = new class {
            public function getName(): string
            {
                return 'default';
            }
        };

        $listener->handle(new CommandExecuted('GET', ['key'], 12.5, $fakeConnection));

        $tracer = $this->app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $spans = $tracer->completedSpans();
        $this->assertCount(1, $spans);
        $this->assertSame('redis.get', $spans[0]->getName());

        $attributes = $spans[0]->toArray()['attributes'];
        $this->assertSame('redis', $attributes['db.system']);
        $this->assertSame('get', $attributes['db.operation']);
        $this->assertSame('default', $attributes['db.connection']);
        $this->assertSame(12.5, $attributes['db.duration_ms']);
    }
}

