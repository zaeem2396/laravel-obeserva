<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Causation;

use Obeserva\DeveloperExperience\Analysis\SpanCategory;
use Obeserva\DeveloperExperience\Analysis\SpanCategoryResolver;
use Obeserva\DeveloperExperience\TraceSnapshot;

final readonly class SlowRequestAnalyzer
{
    public function __construct(
        private SpanCategoryResolver $categoryResolver,
    ) {}

    /**
     * @param  list<TraceSnapshot>  $snapshots
     * @return list<string>
     */
    public function rootCauseSpanIds(array $snapshots, float $thresholdMs, int $limit = 5): array
    {
        if ($snapshots === []) {
            return [];
        }

        $requestDurationMs = $this->requestDurationMs($snapshots);

        if ($requestDurationMs < $thresholdMs) {
            return [];
        }

        $candidates = [];

        foreach ($snapshots as $snapshot) {
            if ($this->categoryResolver->resolve($snapshot) === SpanCategory::Http) {
                continue;
            }

            if ($snapshot->duration === null || $snapshot->duration <= 0) {
                continue;
            }

            $candidates[] = [
                'span_id' => $snapshot->spanId,
                'duration_ms' => $snapshot->duration * 1000,
            ];
        }

        usort(
            $candidates,
            static fn (array $left, array $right): int => $right['duration_ms'] <=> $left['duration_ms'],
        );

        if ($limit <= 0) {
            return [];
        }

        return array_map(
            static fn (array $candidate): string => $candidate['span_id'],
            array_slice($candidates, 0, $limit),
        );
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public function requestDurationMs(array $snapshots): float
    {
        $maxDuration = 0.0;

        foreach ($snapshots as $snapshot) {
            if ($this->categoryResolver->resolve($snapshot) !== SpanCategory::Http) {
                continue;
            }

            if ($snapshot->duration !== null) {
                $maxDuration = max($maxDuration, $snapshot->duration * 1000);
            }

            if (isset($snapshot->attributes['http.duration_ms']) && is_numeric($snapshot->attributes['http.duration_ms'])) {
                $maxDuration = max($maxDuration, (float) $snapshot->attributes['http.duration_ms']);
            }
        }

        if ($maxDuration > 0) {
            return round($maxDuration, 2);
        }

        $total = 0.0;

        foreach ($snapshots as $snapshot) {
            if ($snapshot->duration !== null) {
                $total += $snapshot->duration * 1000;
            }
        }

        return round($total, 2);
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public function isSlowRequest(array $snapshots, float $thresholdMs): bool
    {
        return $this->requestDurationMs($snapshots) >= $thresholdMs;
    }
}
