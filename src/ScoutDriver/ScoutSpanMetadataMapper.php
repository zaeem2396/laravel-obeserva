<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Span\SpanInterface;

final class ScoutSpanMetadataMapper
{
    /** @var array<string, string> */
    private const ATTRIBUTE_TO_SCOUT_TAG = [
        'laravel.route.name' => 'scout.route.name',
        'laravel.route.action' => 'scout.route.action',
        'http.route' => 'scout.route.uri',
        'http.method' => 'scout.http.method',
        'http.status_code' => 'scout.http.status_code',
        'queue.name' => 'scout.queue.name',
        'queue.job' => 'scout.queue.job',
        'queue.connection' => 'scout.queue.connection',
        'queue.uuid' => 'scout.queue.uuid',
        'queue.result' => 'scout.queue.result',
        'horizon.supervisor' => 'scout.horizon.supervisor',
        'horizon.job_id' => 'scout.horizon.job_id',
        'horizon.worker.status' => 'scout.horizon.worker.status',
        'horizon.event' => 'scout.horizon.event',
        'horizon.retry_of' => 'scout.horizon.retry_of',
    ];

    /**
     * @return array<string, string>
     */
    public function map(SpanInterface $span): array
    {
        $tags = [];

        foreach ($span->getAttributes() as $key => $value) {
            if (! isset(self::ATTRIBUTE_TO_SCOUT_TAG[$key])) {
                continue;
            }

            if (! is_scalar($value) || $value === null || $value === '') {
                continue;
            }

            $tags[self::ATTRIBUTE_TO_SCOUT_TAG[$key]] = (string) $value;
        }

        return $tags;
    }
}
