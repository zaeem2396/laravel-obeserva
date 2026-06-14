<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

final class LaravelTelescopePublisher implements TelescopePublisherInterface
{
    public function publish(array $entry): void
    {
        if (! class_exists('Laravel\Telescope\Telescope')
            || ! class_exists('Laravel\Telescope\IncomingEntry')) {
            return;
        }

        Telescope::recordIncoming(
            IncomingEntry::make([
                'type' => 'obeserva',
                'content' => $entry,
            ]),
        );
    }
}
