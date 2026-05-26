<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Trace;

final class SpanIds
{
    public static function generateTraceId(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function generateSpanId(): string
    {
        return bin2hex(random_bytes(8));
    }
}
