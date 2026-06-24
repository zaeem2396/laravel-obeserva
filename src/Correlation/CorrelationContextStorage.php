<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Correlation;

final class CorrelationContextStorage
{
    private ?string $correlationId = null;

    public function set(?string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    public function get(): ?string
    {
        return $this->correlationId;
    }

    public function resolve(CorrelationIdGenerator $generator): string
    {
        if ($this->correlationId !== null && $this->correlationId !== '') {
            return $this->correlationId;
        }

        $this->correlationId = $generator->generate();

        return $this->correlationId;
    }

    public function clear(): void
    {
        $this->correlationId = null;
    }
}
