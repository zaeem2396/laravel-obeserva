<?php

declare(strict_types=1);

namespace Obeserva\OtelDriver;

use Obeserva\Contracts\Span\SpanKind;

final class OtelSpanKindMapper
{
    public function map(SpanKind $kind): string
    {
        return match ($kind) {
            SpanKind::Server => 'SPAN_KIND_SERVER',
            SpanKind::Client => 'SPAN_KIND_CLIENT',
            SpanKind::Consumer => 'SPAN_KIND_CONSUMER',
            SpanKind::Producer => 'SPAN_KIND_PRODUCER',
            SpanKind::Internal => 'SPAN_KIND_INTERNAL',
        };
    }
}
