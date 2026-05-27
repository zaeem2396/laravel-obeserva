<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Queue;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Contracts\Trace\TraceContext;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Queue\QueuePayloadHook;
use PHPUnit\Framework\TestCase;

final class QueuePayloadHookTest extends TestCase
{
    public function test_injects_active_span_context_into_payload(): void
    {
        $context = new ContextManager;
        $span = new Span('http', SpanKind::Server, 'trace1234567890123456789012345678', 'span1234567890ab');
        $context->push($span);

        $hook = new QueuePayloadHook($context, $context);
        $result = $hook('redis', 'default', []);

        $this->assertArrayHasKey('obeserva', $result);
        $obeserva = $result['obeserva'];
        $this->assertIsArray($obeserva);
        $this->assertSame('trace1234567890123456789012345678', $obeserva['trace_id']);
    }

    public function test_uses_stored_context_when_no_active_span(): void
    {
        $context = new ContextManager;
        $context->set(new TraceContext('d'.str_repeat('e', 31), str_repeat('f', 16)));

        $hook = new QueuePayloadHook($context, $context);
        $result = $hook(null, null, []);

        $this->assertArrayHasKey('obeserva', $result);
    }
}
