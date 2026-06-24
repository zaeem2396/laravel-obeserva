<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Causation;

final readonly class CausationEdge
{
    public function __construct(
        public string $fromSpanId,
        public string $toSpanId,
        public string $type = 'parent-child',
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'from' => $this->fromSpanId,
            'to' => $this->toSpanId,
            'type' => $this->type,
        ];
    }
}
