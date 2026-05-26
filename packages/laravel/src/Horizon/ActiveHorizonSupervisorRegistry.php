<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

use Obeserva\Contracts\Span\SpanInterface;

final class ActiveHorizonSupervisorRegistry
{
    /** @var array<string, SpanInterface> */
    private array $spans = [];

    public function get(string $supervisorName): ?SpanInterface
    {
        return $this->spans[$supervisorName] ?? null;
    }

    public function set(string $supervisorName, SpanInterface $span): void
    {
        $this->spans[$supervisorName] = $span;
    }

    public function forget(string $supervisorName): void
    {
        unset($this->spans[$supervisorName]);
    }

    public function clear(): void
    {
        $this->spans = [];
    }

    /**
     * @return list<SpanInterface>
     */
    public function all(): array
    {
        return array_values($this->spans);
    }
}
