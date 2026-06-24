<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Propagation\PropagationContextResolver;

final readonly class QueuePayloadHook
{
    public function __construct(
        private PropagationContextResolver $propagationResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function __invoke(?string $connection, ?string $queue, array $payload): array
    {
        $context = $this->propagationResolver->currentTraceContext();

        if (! $context instanceof TraceContextInterface) {
            return $payload;
        }

        /** @var array<string, mixed> $enriched */
        $enriched = TraceContextCarrier::inject(
            $context,
            $payload,
            $this->propagationResolver->currentCorrelationId(),
        );

        return $enriched;
    }
}
