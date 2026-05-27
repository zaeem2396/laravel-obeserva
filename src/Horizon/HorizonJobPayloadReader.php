<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Horizon;

use Obeserva\Contracts\Span\SpanInterface;

final class HorizonJobPayloadReader
{
    /**
     * @param  array<string, mixed>  $decoded
     */
    public static function enrichSpan(SpanInterface $span, array $decoded): void
    {
        if (isset($decoded['retry_of']) && is_string($decoded['retry_of'])) {
            $span->setAttribute('horizon.retry_of', $decoded['retry_of']);
        }

        $jobId = $decoded['uuid'] ?? $decoded['id'] ?? null;

        if (is_string($jobId) && $jobId !== '') {
            $span->setAttribute('horizon.job_id', $jobId);
        }

        $tags = $decoded['tags'] ?? null;

        if (is_array($tags) && $tags !== []) {
            $span->setAttribute('horizon.tags', implode(',', array_map(
                static fn (mixed $tag): string => is_scalar($tag) ? (string) $tag : '',
                $tags,
            )));
        }
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    public static function retryAttempt(array $decoded): int
    {
        if (isset($decoded['attempts']) && is_numeric($decoded['attempts'])) {
            return max(1, (int) $decoded['attempts']);
        }

        return 1;
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    public static function isRetry(array $decoded): bool
    {
        return isset($decoded['retry_of']) && is_string($decoded['retry_of']);
    }
}
