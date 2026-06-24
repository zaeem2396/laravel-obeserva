<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

use Obeserva\DeveloperExperience\TraceSnapshot;

final class SpanCategoryResolver
{
    public function resolve(TraceSnapshot $snapshot): SpanCategory
    {
        $name = $snapshot->name;

        if ($snapshot->kind === 'server' || isset($snapshot->attributes['http.method'])) {
            return SpanCategory::Http;
        }

        if (str_starts_with($name, 'db.') || isset($snapshot->attributes['db.system'])) {
            return SpanCategory::Database;
        }

        if (str_starts_with($name, 'cache.')) {
            return SpanCategory::Cache;
        }

        if (str_starts_with($name, 'queue.') || isset($snapshot->attributes['queue.name'])) {
            return SpanCategory::Queue;
        }

        if (str_starts_with($name, 'redis.')) {
            return SpanCategory::Redis;
        }

        if (str_starts_with($name, 'event.dispatch:')) {
            return SpanCategory::Event;
        }

        if (str_starts_with($name, 'notification.')) {
            return SpanCategory::Notification;
        }

        if (str_starts_with($name, 'broadcast.')) {
            return SpanCategory::Broadcast;
        }

        if (str_starts_with($name, 'horizon.')) {
            return SpanCategory::Horizon;
        }

        if ($name === 'exception') {
            return SpanCategory::Exception;
        }

        return SpanCategory::Other;
    }
}
