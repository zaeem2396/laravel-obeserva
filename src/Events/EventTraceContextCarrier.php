<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Events;

use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Core\Propagation\TraceCarrierBag;
use Obeserva\Laravel\Events\Concerns\InteractsWithTraceContext;

final class EventTraceContextCarrier
{
    /**
     * @return array<string, mixed>
     */
    public static function inject(
        object $event,
        TraceContextInterface $context,
        ?string $correlationId = null,
    ): array {
        $carrier = self::carrierFromEvent($event);

        $enriched = TraceCarrierBag::inject($context, $carrier, $correlationId);
        self::writeCarrierToEvent($event, $enriched);

        return $enriched;
    }

    public static function extract(object $event): ?TraceContextInterface
    {
        return TraceCarrierBag::extract(self::carrierFromEvent($event));
    }

    public static function extractCorrelationId(object $event): ?string
    {
        return TraceCarrierBag::extractCorrelationId(self::carrierFromEvent($event));
    }

    public static function supports(object $event): bool
    {
        return in_array(InteractsWithTraceContext::class, class_uses_recursive($event), true)
            || property_exists($event, 'obeserva');
    }

    /**
     * @return array<string, mixed>
     */
    private static function carrierFromEvent(object $event): array
    {
        if (property_exists($event, 'obeserva') && is_array($event->obeserva)) {
            /** @var array<string, mixed> $carrier */
            $carrier = $event->obeserva;

            return $carrier;
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $carrier
     */
    /**
     * @param  array<string, mixed>  $carrier
     */
    private static function writeCarrierToEvent(object $event, array $carrier): void
    {
        if (! property_exists($event, 'obeserva')) {
            return;
        }

        $reflection = new \ReflectionProperty($event, 'obeserva');
        $reflection->setValue($event, $carrier);
    }
}
