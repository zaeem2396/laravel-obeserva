<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience;

final readonly class PropagationFlowSummary
{
    /**
     * @param  list<string>  $queueSpans
     * @param  list<string>  $httpSpans
     */
    public function __construct(
        public ?string $traceId,
        public int $spanCount,
        public array $queueSpans,
        public array $httpSpans,
        public bool $hasParentContext,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'trace_id' => $this->traceId,
            'span_count' => $this->spanCount,
            'queue_spans' => $this->queueSpans,
            'http_spans' => $this->httpSpans,
            'has_parent_context' => $this->hasParentContext,
        ];
    }
}
