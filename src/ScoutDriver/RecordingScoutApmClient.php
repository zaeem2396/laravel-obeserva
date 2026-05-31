<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

final class RecordingScoutApmClient implements ScoutApmClientInterface
{
    /** @var list<array{type: string, operation?: string, tag?: string, value?: string}> */
    public array $actions = [];

    public function __construct(
        private readonly bool $enabled = true,
    ) {}

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function startSpan(string $operation, ?float $overrideTimestamp = null): void
    {
        $this->actions[] = [
            'type' => 'startSpan',
            'operation' => $operation,
        ];
    }

    public function stopSpan(): void
    {
        $this->actions[] = ['type' => 'stopSpan'];
    }

    public function addContext(string $tag, string $value): void
    {
        $this->actions[] = [
            'type' => 'addContext',
            'tag' => $tag,
            'value' => $value,
        ];
    }

    public function tagRequest(string $tag, string $value): void
    {
        $this->actions[] = [
            'type' => 'tagRequest',
            'tag' => $tag,
            'value' => $value,
        ];
    }

    public function send(): bool
    {
        $this->actions[] = ['type' => 'send'];

        return true;
    }
}
