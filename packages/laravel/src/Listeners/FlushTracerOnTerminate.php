<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Contracts\Driver\TracerInterface;

final readonly class FlushTracerOnTerminate
{
    public function __construct(
        private TracerInterface $tracer,
    ) {}

    public function handle(): void
    {
        $this->tracer->flush();
    }
}
