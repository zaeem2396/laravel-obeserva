<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

interface TelescopePublisherInterface
{
    /**
     * @param  array<string, mixed>  $entry
     */
    public function publish(array $entry): void;
}
