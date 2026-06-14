<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

final class OtelSemanticConventionMapper
{
    /** @var array<string, string> */
    private const array ATTRIBUTE_MAP = [
        'http.method' => 'http.request.method',
        'http.status_code' => 'http.response.status_code',
        'http.route' => 'http.route',
        'http.url' => 'url.full',
        'http.scheme' => 'url.scheme',
        'http.host' => 'server.address',
        'http.client_ip' => 'client.address',
        'http.user_agent' => 'user_agent.original',
        'http.duration_ms' => 'http.server.request.duration',
        'laravel.route.name' => 'http.route',
        'laravel.route.action' => 'code.function',
        'db.system' => 'db.system.name',
        'db.statement' => 'db.query.text',
        'db.operation' => 'db.operation.name',
        'db.connection' => 'db.namespace',
        'db.duration_ms' => 'db.client.operation.duration',
        'queue.name' => 'messaging.destination.name',
        'queue.job' => 'messaging.operation.name',
        'queue.connection' => 'messaging.system',
        'queue.uuid' => 'messaging.message.id',
        'queue.result' => 'messaging.operation.result',
        'messaging.system' => 'messaging.system',
        'messaging.operation' => 'messaging.operation.type',
        'cache.store' => 'db.system.name',
        'cache.key' => 'db.collection.name',
        'exception.type' => 'exception.type',
        'exception.message' => 'exception.message',
        'user.id' => 'enduser.id',
    ];

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, string>
     */
    public function map(array $attributes): array
    {
        $mapped = [];

        foreach ($attributes as $key => $value) {
            if (! is_scalar($value) && $value !== null) {
                continue;
            }

            $otelKey = self::ATTRIBUTE_MAP[$key] ?? $key;
            $stringValue = (string) $value;

            if ($stringValue === '') {
                continue;
            }

            if ($key === 'http.duration_ms' || $key === 'db.duration_ms') {
                $mapped[$otelKey] = (string) ((float) $value / 1000);

                continue;
            }

            $mapped[$otelKey] = $stringValue;
        }

        return $mapped;
    }
}
