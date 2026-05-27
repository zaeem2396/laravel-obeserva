<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Database;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Database\NPlusOneDetector;
use Obeserva\Laravel\Listeners\NPlusOneDetectionListener;
use PHPUnit\Framework\TestCase;

final class NPlusOneDetectorTest extends TestCase
{
    public function test_detects_repeated_select_pattern(): void
    {
        $detector = new NPlusOneDetector(threshold: 2);
        $sql = "select * from users where id = '1'";

        $this->assertNull($detector->record($sql));
        $pattern = $detector->record("select * from users where id = '2'");

        $this->assertNotNull($pattern);
        $this->assertStringContainsString('select * from users', $pattern);
    }

    public function test_listener_annotates_active_span(): void
    {
        $context = new ContextManager;
        $span = new Span('request', SpanKind::Server, 'trace', 'span');
        $context->push($span);

        $detector = new NPlusOneDetector(threshold: 2);
        $listener = new NPlusOneDetectionListener($context, $detector);

        $listener->recordQuery("select * from posts where user_id = '1'");
        $listener->recordQuery("select * from posts where user_id = '2'");

        $attributes = $span->toArray()['attributes'];
        $this->assertIsArray($attributes);
        $this->assertTrue($attributes['db.n_plus_one_detected']);
    }
}
