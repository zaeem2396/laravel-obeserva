<?php

declare(strict_types=1);

namespace Obeserva\Core\Flush;

use Obeserva\Contracts\Driver\TracerInterface;

final readonly class TracerFlushGuard
{
    public function __construct(
        private TracerInterface $tracer,
        private bool $guardExceptions = true,
    ) {}

    public function flush(): void
    {
        if ($this->guardExceptions) {
            try {
                $this->tracer->flush();
            } catch (\Throwable) {
                // Flush safety: export failures must not break the application.
            }

            return;
        }

        $this->tracer->flush();
    }
}
