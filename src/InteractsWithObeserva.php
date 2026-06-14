<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\DeveloperExperience\TraceSnapshot;
use Obeserva\DeveloperExperience\TraceSnapshotRegistry;
use Obeserva\Laravel\ObeservaServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @mixin TestCase
 */
trait InteractsWithObeserva
{
    protected function swapObeservaTracer(?FakeTracer $tracer = null): FakeTracer
    {
        $tracer ??= new FakeTracer;

        $this->app->instance(TracerInterface::class, $tracer);

        return $tracer;
    }

    /**
     * @return list<TraceSnapshot>
     */
    protected function obeservaTraceSnapshots(): array
    {
        return $this->app->make(TraceSnapshotRegistry::class)->all();
    }

    protected function configureObeservaTesting(): void
    {
        config([
            'obeserva.enabled' => true,
            'obeserva.terminate.flush_tracer' => false,
            'obeserva.development.enabled' => true,
            'obeserva.development.collect_snapshots' => true,
        ]);
    }

    /**
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function obeservaPackageProviders(): array
    {
        return [ObeservaServiceProvider::class];
    }
}
