<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Obeserva\DeveloperExperience\Analysis\TraceSummaryBuilder;
use Obeserva\DeveloperExperience\Analysis\TraceSummaryRegistry;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;

final readonly class BuildTraceSummaryOnTerminate
{
    public function __construct(
        private TraceSnapshotRegistry $snapshotRegistry,
        private TraceSummaryBuilder $summaryBuilder,
        private TraceSummaryRegistry $summaryRegistry,
    ) {}

    public function handle(): void
    {
        if (! (bool) config('obeserva.summaries.enabled', true)) {
            return;
        }

        $snapshots = $this->snapshotRegistry->all();

        if ($snapshots === []) {
            return;
        }

        $threshold = $this->configFloat('obeserva.causation.slow_request_threshold_ms', 1000.0);
        $topSlowSpans = $this->configInt('obeserva.summaries.top_slow_spans', 5);
        $includeCausation = (bool) config('obeserva.causation.enabled', true);

        $summary = $this->summaryBuilder->build(
            $snapshots,
            slowRequestThresholdMs: $threshold,
            topSlowSpans: $topSlowSpans,
            includeCausation: $includeCausation,
        );

        $this->summaryRegistry->store($summary);
    }

    private function configFloat(string $key, float $default): float
    {
        $value = config($key, $default);

        return is_numeric($value) ? (float) $value : $default;
    }

    private function configInt(string $key, int $default): int
    {
        $value = config($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }
}
