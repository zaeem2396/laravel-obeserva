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

    /** @var null|\Closure(self): void */
    private ?\Closure $onEnd = null;

    public function __construct(
        private readonly string $name,
        private readonly SpanKind $kind,
        private readonly string $traceId,
        private readonly string $spanId,
        private readonly ?string $parentSpanId = null,
        ?float $startedAt = null,
    ) {
        $this->startedAt = $startedAt ?? microtime(true);
    }

    /**
     * @param  \Closure(self): void  $callback
     */
    public function whenEnded(\Closure $callback): void
    {
        $this->onEnd = $callback;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKind(): SpanKind
    {
        return $this->kind;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getSpanId(): string
    {
        return $this->spanId;
    }

    public function getParentSpanId(): ?string
    {
        return $this->parentSpanId;
    }

    public function isEnded(): bool
    {
        return $this->endedAt !== null;
    }

    public function getDuration(): ?float
    {
        if ($this->endedAt === null) {
            return null;
        }

        return $this->endedAt - $this->startedAt;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
        if ($this->endedAt !== null) {
            return;
        }

        $this->endedAt = microtime(true);
        ($this->onEnd)?->__invoke($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'kind' => $this->kind->value,
            'trace_id' => $this->traceId,
            'span_id' => $this->spanId,
            'parent_span_id' => $this->parentSpanId,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'duration' => $this->getDuration(),
            'attributes' => $this->attributes,
            'events' => $this->events,
        ];
    }
}
