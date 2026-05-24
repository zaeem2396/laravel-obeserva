<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Trace;

final readonly class TraceContext implements TraceContextInterface
{
    public function __construct(
        private string $traceId,
        private string $spanId,
        private ?string $parentSpanId = null,
        private bool $sampled = true,
    ) {}

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

    public function isSampled(): bool
    {
        return $this->sampled;
    }

    public function toPropagationHeaders(): array
    {
        $flags = $this->sampled ? '01' : '00';

        return [
            'traceparent' => sprintf('00-%s-%s-%s', $this->traceId, $this->spanId, $flags),
        ];
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    public static function fromPropagationHeaders(array $headers): ?self
    {
        $traceparent = $headers['traceparent'] ?? $headers['Traceparent'] ?? null;

        if (! is_string($traceparent)) {
            return null;
        }

        $parts = explode('-', $traceparent);

        if (count($parts) !== 4) {
            return null;
        }

        [, $traceId, $spanId, $flags] = $parts;

        return new self(
            traceId: $traceId,
            spanId: $spanId,
            sampled: $flags === '01',
        );
    }
}
