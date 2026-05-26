<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Routing\Events\RouteMatched;
use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Laravel\Http\RequestSpanEnricher;

final readonly class RouteMatchedListener
{
    public function __construct(
        private ActiveSpanStorageInterface $activeSpanStorage,
        private RequestSpanEnricher $enricher,
    ) {}

    public function handle(RouteMatched $event): void
    {
        $span = $this->activeSpanStorage->current();

        if (! $span instanceof SpanInterface || $span->isEnded()) {
            return;
        }

        $this->enricher->enrichRoute($span, $event->route);
        $span->addEvent('route.matched', [
            'laravel.route.name' => $event->route->getName() ?? '',
        ]);
    }
}
