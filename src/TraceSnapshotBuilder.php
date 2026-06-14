<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\DeveloperExperience\TraceSnapshot;

final class TraceSnapshotBuilder
{
    private string $kind = 'internal';

    private string $traceId = 'trace-test';

    private string $spanId = 'span-test';

    private ?string $parentSpanId = null;

    private float $startedAt = 100.0;

    private ?float $endedAt = 101.0;

    private ?float $duration = 1.0;

    /** @var array<string, mixed> */
    private array $attributes = [];

    /** @var list<array{name: string, attributes: array<string, mixed>}> */
    private array $events = [];

    private function __construct(private readonly string $name) {}

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function kind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function traceId(string $traceId): self
    {
        $this->traceId = $traceId;

        return $this;
    }

    public function spanId(string $spanId): self
    {
        $this->spanId = $spanId;

        return $this;
    }

    public function parentSpanId(?string $parentSpanId): self
    {
        $this->parentSpanId = $parentSpanId;

        return $this;
    }

    public function startedAt(float $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function endedAt(?float $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function duration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function attribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function event(string $name, array $attributes = []): self
    {
        $this->events[] = ['name' => $name, 'attributes' => $attributes];

        return $this;
    }

    public function build(): TraceSnapshot
    {
        return new TraceSnapshot(
            name: $this->name,
            kind: $this->kind,
            traceId: $this->traceId,
            spanId: $this->spanId,
            parentSpanId: $this->parentSpanId,
            startedAt: $this->startedAt,
            endedAt: $this->endedAt,
            duration: $this->duration,
            attributes: $this->attributes,
            events: $this->events,
        );
    }
}
