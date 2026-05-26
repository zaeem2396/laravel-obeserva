<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Database;

final class NPlusOneDetector
{
    /** @var array<string, int> */
    private array $patterns = [];

    public function __construct(
        private readonly int $threshold = 2,
    ) {}

    /**
     * @return non-empty-string|null Pattern key when threshold is reached
     */
    public function record(string $sql): ?string
    {
        $pattern = $this->normalize($sql);
        $this->patterns[$pattern] = ($this->patterns[$pattern] ?? 0) + 1;

        if ($this->patterns[$pattern] === $this->threshold && $pattern !== '') {
            return $pattern;
        }

        return null;
    }

    public function reset(): void
    {
        $this->patterns = [];
    }

    private function normalize(string $sql): string
    {
        $normalized = strtolower($sql);
        $normalized = preg_replace("/'[^']*'/", '?', $normalized) ?? $normalized;
        $normalized = preg_replace('/\b\d+\b/', '?', $normalized) ?? $normalized;

        return preg_replace('/\s+/', ' ', trim($normalized)) ?? $normalized;
    }
}
