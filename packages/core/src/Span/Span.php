<?php

declare(strict_types=1);

namespace Obeserva\Core\Span;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;

final class Span implements SpanInterface
{
    /** @var array<string, mixed> */
    private array $attributes = [];

    /** @var list<array{name: string, attributes: array<string, mixed>}> */
    private array $events = [];

    private ?float $endedAt = null;

    private readonly float $startedAt;

    public function __construct(
        private readonly string $name,
        private readonly SpanKind $kind,
        ?float $startedAt = null,
    ) {
        $this->startedAt = $startedAt ?? microtime(true);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addEvent(string $name, array $attributes = []): void
    {
        $this->events[] = ['name' => $name, 'attributes' => $attributes];
    }

    public function end(): void
    {
        $this->endedAt = microtime(true);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'kind' => $this->kind->value,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'attributes' => $this->attributes,
            'events' => $this->events,
        ];
    }
}
