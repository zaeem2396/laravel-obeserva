<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver\Tests;

use Obeserva\OtelDriver\OtelSemanticConventionMapper;
use PHPUnit\Framework\TestCase;

final class OtelSemanticConventionMapperTest extends TestCase
{
    public function test_maps_http_and_database_attributes(): void
    {
        $mapper = new OtelSemanticConventionMapper;

        $this->assertSame(
            [
                'http.request.method' => 'GET',
                'http.response.status_code' => '200',
                'http.route' => '/users',
                'url.full' => 'https://app.test/users',
            ],
            $mapper->map([
                'http.method' => 'GET',
                'http.status_code' => 200,
                'http.route' => '/users',
                'http.url' => 'https://app.test/users',
            ]),
        );
    }

    public function test_maps_queue_and_messaging_attributes(): void
    {
        $mapper = new OtelSemanticConventionMapper;

        $this->assertSame(
            [
                'messaging.destination.name' => 'emails',
                'messaging.operation.name' => 'App\\Jobs\\SendEmail',
                'messaging.system' => 'redis',
            ],
            $mapper->map([
                'queue.name' => 'emails',
                'queue.job' => 'App\\Jobs\\SendEmail',
                'queue.connection' => 'redis',
            ]),
        );
    }

    public function test_converts_duration_attributes_to_seconds(): void
    {
        $mapper = new OtelSemanticConventionMapper;

        $mapped = $mapper->map([
            'http.duration_ms' => 250,
            'db.duration_ms' => 12.5,
        ]);

        $this->assertSame('0.25', $mapped['http.server.request.duration']);
        $this->assertSame('0.0125', $mapped['db.client.operation.duration']);
    }
}
