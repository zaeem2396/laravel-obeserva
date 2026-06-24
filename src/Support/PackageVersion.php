<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Support;

final class PackageVersion
{
    public const string VERSION = '1.0.0';

    public static function version(): string
    {
        return self::VERSION;
    }
}
