<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Analysis;

enum SpanCategory: string
{
    case Http = 'http';
    case Database = 'database';
    case Cache = 'cache';
    case Queue = 'queue';
    case Redis = 'redis';
    case Event = 'event';
    case Notification = 'notification';
    case Broadcast = 'broadcast';
    case Horizon = 'horizon';
    case Exception = 'exception';
    case Other = 'other';
}
