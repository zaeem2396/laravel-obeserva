<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Span\SpanKind;

final class ScoutSpanMapper
{
    public function operation(string $name, SpanKind $kind): string
    {
        $prefix = match ($kind) {
            SpanKind::Server => 'HTTP',
            SpanKind::Client => 'External',
            SpanKind::Consumer => 'Job',
            SpanKind::Producer => 'Queue',
            SpanKind::Internal => 'Custom',
        };

        return $prefix.'/'.$name;
    }

    public function instrumentType(SpanKind $kind): string
    {
        return match ($kind) {
            SpanKind::Server => 'Controller',
            SpanKind::Client => 'ExternalService',
            SpanKind::Consumer => 'Job',
            SpanKind::Producer => 'Queue',
            SpanKind::Internal => 'Custom',
        };
    }
}
