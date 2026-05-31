<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

/**
 * Adapts a Scout APM agent instance to Obeserva's client interface.
 *
 * @internal
 */
final readonly class ScoutApmAgentAdapter implements ScoutApmClientInterface
{
    public function __construct(
        private object $agent,
    ) {}

    public function enabled(): bool
    {
        if (! method_exists($this->agent, 'enabled')) {
            return false;
        }

        return (bool) $this->agent->enabled();
    }

    public function startSpan(string $operation, ?float $overrideTimestamp = null): void
    {
        if (! method_exists($this->agent, 'startSpan')) {
            return;
        }

        $this->agent->startSpan($operation, $overrideTimestamp);
    }

    public function stopSpan(): void
    {
        if (! method_exists($this->agent, 'stopSpan')) {
            return;
        }

        $this->agent->stopSpan();
    }

    public function addContext(string $tag, string $value): void
    {
        if (! method_exists($this->agent, 'addContext')) {
            return;
        }

        $this->agent->addContext($tag, $value);
    }

    public function tagRequest(string $tag, string $value): void
    {
        if (! method_exists($this->agent, 'tagRequest')) {
            return;
        }

        $this->agent->tagRequest($tag, $value);
    }

    public function send(): bool
    {
        if (! method_exists($this->agent, 'send')) {
            return false;
        }

        return (bool) $this->agent->send();
    }
}
