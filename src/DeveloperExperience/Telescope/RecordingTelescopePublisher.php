<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

final class RecordingTelescopePublisher implements TelescopePublisherInterface
{
    /** @var list<array<string, mixed>> */
    public array $entries = [];

    public function publish(array $entry): void
    {
        $this->entries[] = $entry;
    }
}
