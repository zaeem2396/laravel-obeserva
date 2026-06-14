<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Telescope;

final class NullTelescopePublisher implements TelescopePublisherInterface
{
    public function publish(array $entry): void {}
}
