<?php

declare(strict_types=1);

namespace Obeserva\Contracts\Driver;

use Obeserva\Contracts\Trace\TraceContextInterface;

interface ContextStorageInterface
{
    public function get(): ?TraceContextInterface;

    public function set(?TraceContextInterface $context): void;
}
