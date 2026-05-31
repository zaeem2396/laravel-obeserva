<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Illuminate\Contracts\Foundation\Application;
use Obeserva\Contracts\Driver\SpanLifecycleExporterInterface;
use Obeserva\Core\Export\NoopSpanLifecycleExporter;

final class ContainerScoutApmClient implements ScoutApmClientInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function enabled(): bool
    {
        $agent = $this->resolveAgent();

        return $agent !== null && $agent->enabled();
    }

    public function startSpan(string $operation, ?float $overrideTimestamp = null): void
    {
        $agent = $this->resolveAgent();

        if ($agent === null) {
            return;
        }

        $agent->startSpan($operation, $overrideTimestamp);
    }

    public function stopSpan(): void
    {
        $agent = $this->resolveAgent();

        if ($agent === null) {
            return;
        }

        $agent->stopSpan();
    }

    public function addContext(string $tag, string $value): void
    {
        $agent = $this->resolveAgent();

        if ($agent === null) {
            return;
        }

        $agent->addContext($tag, $value);
    }

    public function tagRequest(string $tag, string $value): void
    {
        $agent = $this->resolveAgent();

        if ($agent === null) {
            return;
        }

        $agent->tagRequest($tag, $value);
    }

    public function send(): bool
    {
        $agent = $this->resolveAgent();

        if ($agent === null) {
            return false;
        }

        return $agent->send();
    }

    private function resolveAgent(): ?object
    {
        if (! interface_exists('Scoutapm\\ScoutApmAgent') || ! $this->app->bound('Scoutapm\\ScoutApmAgent')) {
            return null;
        }

        return $this->app->make('Scoutapm\\ScoutApmAgent');
    }
}
