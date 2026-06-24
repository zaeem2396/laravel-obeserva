<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Queue;

use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Core\Propagation\TraceCarrierBag;

final class TraceContextCarrier
{
    public const string PAYLOAD_KEY = TraceCarrierBag::CARRIER_KEY;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function inject(
        TraceContextInterface $context,
        array $payload,
        ?string $correlationId = null,
    ): array {
        return TraceCarrierBag::inject($context, $payload, $correlationId);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function extract(array $payload): ?TraceContextInterface
    {
        return TraceCarrierBag::extract($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function extractCorrelationId(array $payload): ?string
    {
        return TraceCarrierBag::extractCorrelationId($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public static function decodeJobPayload(string $rawBody): array
    {
        $decoded = json_decode($rawBody, true);

        if (! is_array($decoded)) {
            return [];
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
