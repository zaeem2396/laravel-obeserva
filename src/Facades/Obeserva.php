<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Core\Tracer;
use Obeserva\Laravel\Support\PackageVersion;

/**
 * @method static \Obeserva\Contracts\Span\SpanInterface startSpan(string $name, \Obeserva\Contracts\Span\SpanKind $kind = \Obeserva\Contracts\Span\SpanKind::Internal)
 * @method static \Obeserva\Core\Span\SpanScope trace(string $name, \Obeserva\Contracts\Span\SpanKind $kind = \Obeserva\Contracts\Span\SpanKind::Internal)
 *
 * @see PackageVersion::VERSION
 * @see TracerInterface
 * @see Tracer
 */
final class Obeserva extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TracerInterface::class;
    }
}
