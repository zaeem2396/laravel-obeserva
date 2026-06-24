<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Causation;

use Obeserva\DeveloperExperience\Analysis\SpanCategory;

final readonly class CausationNode
{
    public function __construct(
        public string $spanId,
        public string $name,
        public SpanCategory $category,
        public float $durationMs,
        public bool $isRootCause = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'span_id' => $this->spanId,
            'name' => $this->name,
            'category' => $this->category->value,
            'duration_ms' => $this->durationMs,
            'is_root_cause' => $this->isRootCause,
        ];
    }
}
