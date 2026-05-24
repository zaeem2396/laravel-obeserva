<?php

declare(strict_types=1);

namespace Obeserva\Core\Context;

use Obeserva\Contracts\Driver\ContextStorageInterface;
use Obeserva\Contracts\Trace\TraceContextInterface;

final class ContextManager implements ContextStorageInterface
{
    private ?TraceContextInterface $context = null;

    public function get(): ?TraceContextInterface
    {
        return $this->context;
    }

    public function set(?TraceContextInterface $context): void
    {
        $this->context = $context;
    }

    public function clear(): void
    {
        $this->context = null;
    }
}
