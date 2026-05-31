<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\ScoutDriver\ScoutSpanMapper;
use PHPUnit\Framework\TestCase;

final class ScoutSpanMapperTest extends TestCase
{
    public function test_maps_server_spans_to_http_operations(): void
    {
        $mapper = new ScoutSpanMapper;

        $this->assertSame('HTTP/users.index', $mapper->operation('users.index', SpanKind::Server));
        $this->assertSame('Controller', $mapper->instrumentType(SpanKind::Server));
    }

    public function test_maps_client_spans_to_external_operations(): void
    {
        $mapper = new ScoutSpanMapper;

        $this->assertSame('External/db.select', $mapper->operation('db.select', SpanKind::Client));
        $this->assertSame('ExternalService', $mapper->instrumentType(SpanKind::Client));
    }

    public function test_maps_consumer_spans_to_job_operations(): void
    {
        $mapper = new ScoutSpanMapper;

        $this->assertSame('Job/queue.process:SendEmail', $mapper->operation('queue.process:SendEmail', SpanKind::Consumer));
    }
}
