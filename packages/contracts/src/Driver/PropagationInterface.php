<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

use Obeserva\Contracts\Trace\TraceContextInterface;

interface PropagationInterface
{
    /**
     * @param  array<string, mixed>  $carrier
     * @return array<string, mixed>
     */
    public function inject(TraceContextInterface $context, array $carrier): array;

    /**
     * @param  array<string, mixed>  $carrier
     */
    public function extract(array $carrier): ?TraceContextInterface;
}
