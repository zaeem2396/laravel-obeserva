<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

use Obeserva\DeveloperExperience\Analysis\TraceSummaryRegistry;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;

final readonly class PublishTraceToTelescope
{
    public function __construct(
        private TraceSnapshotRegistry $registry,
        private PropagationFlowInspector $propagationInspector,
        private TraceSummaryRegistry $summaryRegistry,
        private TelescopeTraceEntryFactory $entryFactory,
        private TelescopePublisherInterface $publisher,
    ) {}

    public function handle(): void
    {
        if (! config('obeserva.development.telescope.enabled', false)) {
            return;
        }

        $snapshots = $this->registry->all();

        if ($snapshots === []) {
            return;
        }

        $propagation = $this->propagationInspector->summarize($snapshots);
        $entry = $this->entryFactory->makeEntry($snapshots, $propagation, $this->summaryRegistry->latest());

        $this->publisher->publish($entry);
    }
}
