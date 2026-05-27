<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;

final readonly class TraceCacheEventListener
{
    public function __construct(
        private TracerInterface $tracer,
    ) {}

    public function handle(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): void
    {
        $spanName = match (true) {
            $event instanceof CacheHit => 'cache.get',
            $event instanceof CacheMissed => 'cache.miss',
            $event instanceof KeyWritten => 'cache.put',
            $event instanceof KeyForgotten => 'cache.forget',
        };

        $span = $this->tracer->startSpan($spanName, SpanKind::Client);

        $span->setAttribute('cache.store', (string) ($event->storeName ?? ''));
        $span->setAttribute('cache.key', (string) $event->key);

        if ($event instanceof CacheHit) {
            $span->setAttribute('cache.hit', true);
        }

        if ($event instanceof CacheMissed) {
            $span->setAttribute('cache.hit', false);
        }

        if ($event instanceof KeyWritten) {
            $span->setAttribute('cache.ttl_seconds', $event->seconds);
        }

        $span->end();
    }
}

