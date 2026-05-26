<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Listeners\TraceQueryListener;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

final class TraceQueryListenerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ObeservaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('obeserva.enabled', true);
        $app['config']->set('obeserva.database.query_tracing', true);
        $app['config']->set('obeserva.terminate.flush_tracer', false);
    }

    public function test_query_executed_event_creates_db_span(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $connection = $this->createMock(Connection::class);
        $connection->method('getName')->willReturn('testing');
        $connection->method('getDriverName')->willReturn('sqlite');

        $event = new QueryExecuted(
            'select * from items where id = ?',
            [1],
            0.5,
            $connection,
        );

        $app->make(TraceQueryListener::class)->handle($event);

        $tracer = $app->make(TracerInterface::class);
        $this->assertInstanceOf(Tracer::class, $tracer);

        $completed = $tracer->completedSpans();
        $this->assertCount(1, $completed);
        $this->assertSame('db.select', $completed[0]->getName());

        $attributes = $completed[0]->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertSame('sqlite', $attributes['db.system']);
        $this->assertSame('select', $attributes['db.operation']);
    }
}
