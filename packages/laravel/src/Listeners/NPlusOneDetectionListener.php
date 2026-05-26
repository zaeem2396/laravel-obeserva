<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Laravel\Database\NPlusOneDetector;

final readonly class NPlusOneDetectionListener
{
    public function __construct(
        private ActiveSpanStorageInterface $activeSpanStorage,
        private NPlusOneDetector $detector,
    ) {}

    public function recordQuery(string $sql): void
    {
        $pattern = $this->detector->record($sql);

        if ($pattern === null) {
            return;
        }

        $active = $this->activeSpanStorage->current();

        if (! $active instanceof SpanInterface || $active->isEnded()) {
            return;
        }

        $active->addEvent('n_plus_one_detected', [
            'db.query.pattern' => $pattern,
        ]);

        $active->setAttribute('db.n_plus_one_detected', true);
        $active->setAttribute('db.n_plus_one.pattern', $pattern);
    }
}
