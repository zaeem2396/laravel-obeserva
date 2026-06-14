<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

enum WorkerRuntime: string
{
    case Http = 'http';
    case QueueWorker = 'queue_worker';
    case Octane = 'octane';
    case RoadRunner = 'roadrunner';
}
