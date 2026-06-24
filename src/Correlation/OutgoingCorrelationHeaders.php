<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Correlation;

use Symfony\Component\HttpFoundation\Response;

final readonly class OutgoingCorrelationHeaders
{
    public function __construct(
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function apply(Response $response, string $header = 'X-Correlation-ID'): void
    {
        $correlationId = $this->correlationStorage->get();

        if ($correlationId === null || $correlationId === '') {
            return;
        }

        $response->headers->set($header, $correlationId);
    }
}
