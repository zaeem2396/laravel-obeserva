<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

final class Horizon
{
    public const string SUPERVISOR_LOOPED = 'Laravel\Horizon\Events\SupervisorLooped';

    public const string WORKER_PROCESS_RESTARTING = 'Laravel\Horizon\Events\WorkerProcessRestarting';

    public const string SUPERVISOR_PROCESS_RESTARTING = 'Laravel\Horizon\Events\SupervisorProcessRestarting';

    public const string JOB_RESERVED = 'Laravel\Horizon\Events\JobReserved';

    public const string JOB_RELEASED = 'Laravel\Horizon\Events\JobReleased';

    public static function isAvailable(): bool
    {
        return class_exists(self::SUPERVISOR_LOOPED);
    }
}
