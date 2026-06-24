<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Events\Concerns;

trait InteractsWithTraceContext
{
    /** @var array<string, mixed>|null */
    public ?array $obeserva = null;
}
