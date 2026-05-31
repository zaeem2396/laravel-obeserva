<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Illuminate\Contracts\Foundation\Application;

final readonly class ContainerScoutApmClient implements ScoutApmClientInterface
{
    public function __construct(
        private Application $app,
    ) {}

    public function enabled(): bool
    {
        $agent = $this->resolveAgent();

        return $agent?->enabled() ?? false;
    }

    public function startSpan(string $operation, ?float $overrideTimestamp = null): void
    {
        $this->resolveAgent()?->startSpan($operation, $overrideTimestamp);
    }

    public function stopSpan(): void
    {
        $this->resolveAgent()?->stopSpan();
    }

    public function addContext(string $tag, string $value): void
    {
        $this->resolveAgent()?->addContext($tag, $value);
    }

    public function tagRequest(string $tag, string $value): void
    {
        $this->resolveAgent()?->tagRequest($tag, $value);
    }

    public function send(): bool
    {
        return $this->resolveAgent()?->send() ?? false;
    }

    private function resolveAgent(): ?ScoutApmAgentAdapter
    {
        if (! interface_exists('Scoutapm\\ScoutApmAgent') || ! $this->app->bound('Scoutapm\\ScoutApmAgent')) {
            return null;
        }

        $agent = $this->app->make('Scoutapm\\ScoutApmAgent');

        if (! is_object($agent)) {
            return null;
        }

        return new ScoutApmAgentAdapter($agent);
    }
}
