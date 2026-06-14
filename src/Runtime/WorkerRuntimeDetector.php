<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Runtime;

final class WorkerRuntimeDetector
{
    public function current(): WorkerRuntime
    {
        if ($this->isOctane()) {
            return WorkerRuntime::Octane;
        }

        if ($this->isRoadRunner()) {
            return WorkerRuntime::RoadRunner;
        }

        if ($this->isDedicatedQueueWorker()) {
            return WorkerRuntime::QueueWorker;
        }

        return WorkerRuntime::Http;
    }

    public function shouldIsolateAfterJob(): bool
    {
        if (! (bool) config('obeserva.worker.context_isolation', true)) {
            return false;
        }

        return $this->current() === WorkerRuntime::QueueWorker;
    }

    public function shouldIsolateOnLongRunningRequestEnd(): bool
    {
        if (! (bool) config('obeserva.worker.context_isolation', true)) {
            return false;
        }

        $runtime = $this->current();

        if ($runtime === WorkerRuntime::Octane && ! (bool) config('obeserva.worker.octane_isolation', true)) {
            return false;
        }

        if ($runtime === WorkerRuntime::RoadRunner && ! (bool) config('obeserva.worker.roadrunner_isolation', true)) {
            return false;
        }

        return in_array($runtime, [WorkerRuntime::Octane, WorkerRuntime::RoadRunner], true);
    }

    public function isOctane(): bool
    {
        return isset($_SERVER['LARAVEL_OCTANE'])
            || (defined('LARAVEL_OCTANE') && LARAVEL_OCTANE === 1);
    }

    public function isRoadRunner(): bool
    {
        return isset($_SERVER['RR_MODE']) || isset($_SERVER['RR_WORKER_MODE']);
    }

    public function isDedicatedQueueWorker(): bool
    {
        if (! app()->runningInConsole()) {
            return false;
        }

        /** @var list<string> $argv */
        $argv = $_SERVER['argv'] ?? [];
        $command = implode(' ', $argv);

        return str_contains($command, 'queue:work')
            || str_contains($command, 'horizon:work')
            || str_contains($command, 'horizon:supervisor');
    }
}
