<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\ScoutDriver\ScoutSpanMetadataMapper;
use PHPUnit\Framework\TestCase;

final class ScoutSpanMetadataMapperTest extends TestCase
{
    public function test_maps_http_and_route_attributes(): void
    {
        $mapper = new ScoutSpanMetadataMapper;
        $span = new Span('http.request', SpanKind::Server, 't', 's');
        $span->setAttribute('laravel.route.name', 'users.index');
        $span->setAttribute('http.method', 'GET');
        $span->setAttribute('http.status_code', 200);

        $this->assertSame(
            [
                'scout.route.name' => 'users.index',
                'scout.http.method' => 'GET',
                'scout.http.status_code' => '200',
            ],
            $mapper->map($span),
        );
    }

    public function test_maps_queue_and_horizon_attributes(): void
    {
        $mapper = new ScoutSpanMetadataMapper;
        $span = new Span('queue.process', SpanKind::Consumer, 't', 's');
        $span->setAttribute('queue.name', 'emails');
        $span->setAttribute('queue.job', 'App\\Jobs\\SendEmail');
        $span->setAttribute('horizon.supervisor', 'supervisor-1');
        $span->setAttribute('horizon.job_id', 'job-abc');

        $this->assertSame(
            [
                'scout.queue.name' => 'emails',
                'scout.queue.job' => 'App\\Jobs\\SendEmail',
                'scout.horizon.supervisor' => 'supervisor-1',
                'scout.horizon.job_id' => 'job-abc',
            ],
            $mapper->map($span),
        );
    }

    public function test_skips_unknown_and_empty_attributes(): void
    {
        $mapper = new ScoutSpanMetadataMapper;
        $span = new Span('custom', SpanKind::Internal, 't', 's');
        $span->setAttribute('custom.field', 'ignored');
        $span->setAttribute('queue.name', '');

        $this->assertSame([], $mapper->map($span));
    }
}
