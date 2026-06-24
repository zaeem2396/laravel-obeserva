<?php

declare(strict_types=1);

namespace Obeserva\Core\Propagation;

use Obeserva\Contracts\Driver\PropagationInterface;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Contracts\Trace\TraceContextInterface;

final class W3cTracePropagator implements PropagationInterface
{
    /**
     * @param  array<string, mixed>  $carrier
     * @return array<string, mixed>
     */
    public function inject(TraceContextInterface $context, array $carrier): array
    {
        return TraceCarrierBag::inject($context, $carrier);
    }

    /**
     * @param  array<string, mixed>  $carrier
     */
    public function extract(array $carrier): ?TraceContextInterface
    {
        $fromBag = TraceCarrierBag::extract($carrier);

        if ($fromBag instanceof TraceContextInterface) {
            return $fromBag;
        }

        return TraceContext::fromPropagationHeaders($carrier);
    }
}
