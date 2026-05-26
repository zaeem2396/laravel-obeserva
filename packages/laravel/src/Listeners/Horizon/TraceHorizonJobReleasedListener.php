<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Horizon;

use Obeserva\Laravel\Horizon\HorizonThroughputMetrics;

final readonly class TraceHorizonJobReleasedListener
{
    public function __construct(
        private HorizonThroughputMetrics $metrics,
    ) {}

    public function handle(): void
    {
        $this->metrics->recordJobReleased();
    }
}
