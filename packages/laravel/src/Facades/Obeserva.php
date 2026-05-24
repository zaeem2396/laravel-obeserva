<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Obeserva\Contracts\Driver\TracerInterface;

/**
 * @method static \Obeserva\Contracts\Span\SpanInterface startSpan(string $name, \Obeserva\Contracts\Span\SpanKind $kind = \Obeserva\Contracts\Span\SpanKind::Internal)
 *
 * @see TracerInterface
 */
final class Obeserva extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TracerInterface::class;
    }
}
