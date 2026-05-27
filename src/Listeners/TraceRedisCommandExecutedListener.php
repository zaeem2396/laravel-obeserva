<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Redis\Events\CommandExecuted;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;

final readonly class TraceRedisCommandExecutedListener
{
    public function __construct(
        private TracerInterface $tracer,
    ) {}

    public function handle(CommandExecuted $event): void
    {
        $command = strtolower((string) $event->command);
        $span = $this->tracer->startSpan('redis.'.$command, SpanKind::Client);

        $span->setAttribute('db.system', 'redis');
        $span->setAttribute('db.operation', $command);
        $span->setAttribute('db.connection', (string) $event->connectionName);
        $span->setAttribute('db.duration_ms', $event->time);

        $span->end();
    }
}

