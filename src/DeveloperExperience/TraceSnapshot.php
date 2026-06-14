<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final readonly class TraceSnapshot
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<array{name: string, attributes: array<string, mixed>}>  $events
     */
    public function __construct(
        public string $name,
        public string $kind,
        public string $traceId,
        public string $spanId,
        public ?string $parentSpanId,
        public float $startedAt,
        public ?float $endedAt,
        public ?float $duration,
        public array $attributes,
        public array $events,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'kind' => $this->kind,
            'trace_id' => $this->traceId,
            'span_id' => $this->spanId,
            'parent_span_id' => $this->parentSpanId,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'duration' => $this->duration,
            'attributes' => $this->attributes,
            'events' => $this->events,
        ];
    }
}
