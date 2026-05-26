<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Obeserva\Contracts\Driver\ActiveSpanStorageInterface;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Database\QueryCounter;
use Obeserva\Laravel\Database\QueryOperation;
use Obeserva\Laravel\Database\QuerySanitizer;
final readonly class TraceQueryListener
{
    public function __construct(
        private TracerInterface $tracer,
        private ActiveSpanStorageInterface $activeSpanStorage,
        private QuerySanitizer $sanitizer,
        private QueryCounter $queryCounter,
        private NPlusOneDetectionListener $nPlusOneDetection,
    ) {}

    public function handle(QueryExecuted $event): void
    {
        $operation = QueryOperation::fromSql($event->sql);
        $span = $this->tracer->startSpan('db.'.$operation, SpanKind::Client);

        $span->setAttribute('db.system', $event->connection->getDriverName());
        $span->setAttribute('db.connection', $event->connectionName);
        $span->setAttribute('db.operation', $operation);
        $span->setAttribute('db.statement', $this->sanitizer->sanitize($event->sql, $event->bindings));
        $span->setAttribute('db.duration_ms', $event->time);

        $span->end();

        $this->recordQueryOnActiveSpan();

        if (config('obeserva.database.lazy_loading_detection', true)) {
            $this->nPlusOneDetection->recordQuery($event->sql);
        }
    }

    private function recordQueryOnActiveSpan(): void
    {
        $active = $this->activeSpanStorage->current();

        if (! $active instanceof SpanInterface || $active->isEnded()) {
            return;
        }

        $count = $this->queryCounter->increment();
        $active->setAttribute('db.query_count', $count);
    }
}
