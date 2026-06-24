# Installation

## Laravel application

```bash
composer require scout/laravel
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=obeserva-config
```

### Environment variables

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_ENABLED` | `true` | Master switch |
| `OBESERVA_DRIVER` | `noop` | Driver identifier (`noop`, `scout`, or `otel`) |
| `OBESERVA_SCOUT_ENABLED` | `true` | Enable Scout export when driver is `scout` |
| `OBESERVA_SCOUT_APPLICATION_NAME` | `APP_NAME` | Scout application name |
| `OBESERVA_SCOUT_KEY` | `SCOUT_KEY` | Scout application key |
| `OBESERVA_SCOUT_MONITORING_ENABLED` | `SCOUT_MONITORING_ENABLED` | Scout monitoring toggle |
| `OBESERVA_SCOUT_DEPLOYMENT_VERSION` | `APP_VERSION` | Deployment/build version tag |
| `OBESERVA_SCOUT_TENANT_ID` | _(empty)_ | Multi-tenant identifier tag |
| `OBESERVA_SCOUT_METADATA_ENABLED` | `true` | Laravel-aware `scout.*` metadata enrichment |
| `OBESERVA_OTEL_ENABLED` | `true` | Enable OTel export when driver is `otel` |
| `OBESERVA_OTEL_SERVICE_NAME` | `APP_NAME` | OTel `service.name` resource attribute |
| `OBESERVA_OTEL_SERVICE_VERSION` | `APP_VERSION` | OTel `service.version` resource attribute |
| `OBESERVA_OTEL_SEMANTIC_CONVENTIONS` | `true` | Normalize span attributes to OTel semantic conventions |
| `OBESERVA_SAMPLE_RATE` | `1.0` | Sampling probability (0.0–1.0) |
| `OBESERVA_HTTP_MIDDLEWARE` | `true` | Register HTTP trace middleware |
| `OBESERVA_HTTP_MIDDLEWARE_TIMING` | `true` | Register `obeserva.timing` middleware alias |
| `OBESERVA_EXCEPTION_INSTRUMENTATION` | `true` | Hook Laravel reportable exceptions |
| `OBESERVA_FLUSH_ON_TERMINATE` | `true` | Flush tracer completed spans on terminate |
| `OBESERVA_DB_QUERY_TRACING` | `true` | Record `db.*` spans for SQL queries |
| `OBESERVA_DB_LAZY_LOADING_DETECTION` | `true` | Detect repeated query patterns (N+1) |
| `OBESERVA_QUEUE_PROPAGATION` | `true` | Inject trace context into queue payloads |
| `OBESERVA_QUEUE_JOB_TRACING` | `true` | Consumer spans for job processing |
| `OBESERVA_QUEUE_FAILED_CORRELATION` | `true` | Correlate failed jobs to traces |
| `OBESERVA_HORIZON_ENABLED` | `true` | Horizon instrumentation (requires `laravel/horizon`) |
| `OBESERVA_HORIZON_WORKER_TRACING` | `true` | Supervisor/worker lifecycle spans |
| `OBESERVA_HORIZON_THROUGHPUT_METRICS` | `true` | Job reserved/released counters on spans |
| `OBESERVA_HORIZON_RETRY_CORRELATION` | `true` | Root trace + retry attempt attributes |
| `OBESERVA_CACHE_ENABLED` | `true` | Cache instrumentation (Cache hit/miss/write/forget) |
| `OBESERVA_REDIS_COMMAND_TRACING` | `true` | Redis command spans via `CommandExecuted` events |
| `OBESERVA_WORKER_CONTEXT_ISOLATION` | `true` | Reset tracer/context after each job in dedicated queue workers |
| `OBESERVA_WORKER_FLUSH_AFTER_JOB` | `true` | Flush completed spans after each job when worker isolation is active |
| `OBESERVA_OCTANE_ISOLATION` | `true` | Reset tracer/context on Octane request/task/tick termination |
| `OBESERVA_ROADRUNNER_ISOLATION` | `true` | Reset tracer/context on RoadRunner worker termination events |
| `OBESERVA_TELESCOPE_ENABLED` | `false` | Publish span snapshots to Laravel Telescope on terminate |
| `OBESERVA_DEBUG_TOOLBAR` | `APP_DEBUG && APP_ENV=local` | Inject local HTML trace toolbar into HTML responses |
| `OBESERVA_DEBUG_TOOLBAR_PROPAGATION` | `true` | Show queue/HTTP propagation summary in the debug toolbar |
| `OBESERVA_EVENT_PROPAGATION` | `true` | Inject trace context into dispatched application events |
| `OBESERVA_EVENT_TRACING` | `true` | Record spans for application event dispatch |
| `OBESERVA_NOTIFICATION_TRACING` | `true` | Record notification send/sent spans |
| `OBESERVA_BROADCAST_TRACING` | `true` | Record broadcast dispatch spans |
| `OBESERVA_BROADCAST_PROPAGATION` | `true` | Inject trace context into broadcastable events |
| `OBESERVA_CORRELATION_ENABLED` | `true` | Resolve and propagate correlation IDs |
| `OBESERVA_CORRELATION_HEADER` | `X-Correlation-ID` | HTTP header for correlation IDs |
| `OBESERVA_CORRELATION_PROPAGATE` | `true` | Echo correlation ID on HTTP responses |

## Releases

Stable versions are tagged on GitHub as `vX.Y.Z` (latest: `v0.9.0`). See [RELEASE.md](RELEASE.md) and the [v0.9.0 announcement](posts/v0.9.0-ai-advanced-features.md).

### Scout driver

Set `OBESERVA_DRIVER=scout` and install the optional Scout agent:

```bash
composer require scoutapp/scout-apm-laravel
```

Obeserva forwards span lifecycle events to Scout via `ScoutSpanExporter`. When the Scout agent is bound in the container (`Scoutapm\ScoutApmAgent`), spans are exported on flush; otherwise export is skipped safely.

Configuration: `config/obeserva.php` → `scout.*` (see environment variables above).

When `OBESERVA_SCOUT_METADATA_ENABLED=true` (default), Obeserva enriches Scout with `scout.route.name`, `scout.queue.*`, `scout.horizon.*`, deployment version, tenant ID, and PHP/Laravel runtime diagnostics.

### OpenTelemetry driver

Set `OBESERVA_DRIVER=otel` for OTel-compatible span export. Spans are converted on end and batched on flush — no changes to Laravel instrumentation are required.

Optional: install `open-telemetry/opentelemetry` for OTLP export to a collector.

Configuration: `config/obeserva.php` → `otel.*` (see environment variables above).

### Worker context isolation

Long-running queue, Horizon, Octane, and RoadRunner workers reset tracer and context state between jobs and requests so spans do not leak across worker cycles.

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_WORKER_CONTEXT_ISOLATION` | `true` | Enable worker context isolation |
| `OBESERVA_WORKER_FLUSH_AFTER_JOB` | `true` | Flush tracer after each job in dedicated workers |
| `OBESERVA_OCTANE_ISOLATION` | `true` | Reset on Octane termination events |
| `OBESERVA_ROADRUNNER_ISOLATION` | `true` | Reset on RoadRunner termination events |

Configuration: `config/obeserva.php` → `worker.*`.

### Developer experience

Enable local trace inspection without changing your production driver:

```bash
# Optional Telescope integration
composer require laravel/telescope --dev
```

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_TELESCOPE_ENABLED` | `false` | Publish span snapshots to Telescope on terminate |
| `OBESERVA_DEBUG_TOOLBAR` | local + `APP_DEBUG` | Inject HTML trace toolbar into responses |
| `OBESERVA_DEBUG_TOOLBAR_PROPAGATION` | `true` | Show queue/HTTP propagation summary in toolbar |

When either feature is enabled, `SpanSnapshotCollector` records completed spans via `CompositeSpanLifecycleExporter` alongside the configured driver exporter.

Configuration: `config/obeserva.php` → `development.*`.

### Testing utilities

Obeserva includes PHPUnit helpers under `Obeserva\Testing` for asserting spans, propagation, and snapshot flows in package tests:

- `FakeTracer` — in-memory tracer with span and snapshot assertions
- `TraceContextAssert` — W3C traceparent and queue payload propagation checks
- `TraceSnapshotBuilder` / `TraceSnapshotAssert` — snapshot fixtures and hierarchy checks
- `InteractsWithObeserva` — Laravel Testbench trait to swap the tracer

See [Modules](PACKAGES.md#testing-utilities-v071) for details.

### Distributed systems (v0.8.0)

Event propagation, notification/broadcast tracing, and cross-service correlation:

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_EVENT_PROPAGATION` | `true` | Inject trace context into application events |
| `OBESERVA_EVENT_TRACING` | `true` | Record `event.dispatch` spans |
| `OBESERVA_NOTIFICATION_TRACING` | `true` | Record notification spans |
| `OBESERVA_BROADCAST_TRACING` | `true` | Record broadcast dispatch spans |
| `OBESERVA_BROADCAST_PROPAGATION` | `true` | Propagate context into broadcastable events |
| `OBESERVA_CORRELATION_ENABLED` | `true` | Enable `X-Correlation-ID` resolution |
| `OBESERVA_CORRELATION_HEADER` | `X-Correlation-ID` | Correlation HTTP header name |
| `OBESERVA_CORRELATION_PROPAGATE` | `true` | Echo correlation ID on responses |

Add `InteractsWithTraceContext` to application events that should carry trace context across listeners and queued workflows.

Configuration: `config/obeserva.php` → `events.*`, `notifications.*`, `broadcasts.*`, `correlation.*`.

See [v0.8.0 announcement](posts/v0.8.0-distributed-systems.md).

### Production engineering (v0.8.1)

Memory bounds and flush safety for long-running workers:

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_MAX_COMPLETED_SPANS` | `2048` | Auto-flush tracer when completed span buffer reaches this limit (`0` disables) |
| `OBESERVA_MAX_ACTIVE_SPAN_DEPTH` | `256` | End oldest active span when nesting exceeds this depth (`0` disables) |
| `OBESERVA_MAX_TRACE_SNAPSHOTS` | `512` | Evict oldest trace snapshots in development registry (`0` disables) |
| `OBESERVA_MEMORY_PRESSURE_BYTES` | `0` | Auto-flush when RSS exceeds this threshold (`0` disables) |
| `OBESERVA_FLUSH_SAFETY` | `true` | Master toggle for production flush safety features |
| `OBESERVA_FLUSH_GUARD_EXCEPTIONS` | `true` | Swallow exporter exceptions during flush |
| `OBESERVA_FLUSH_ON_SHUTDOWN` | `true` | Flush tracer on PHP shutdown |
| `OBESERVA_FLUSH_ON_WORKER_STOPPING` | `true` | Reset tracer when queue workers stop |

Configuration: `config/obeserva.php` → `memory.*`, `flush.*`.

See [v0.8.1 announcement](posts/v0.8.1-production-engineering.md).

### AI/advanced features (v0.9.0)

Trace summaries and slow-request causation for debugging and LLM workflows:

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_TRACE_SUMMARIES` | `true` | Build structured trace summaries on terminate |
| `OBESERVA_SUMMARY_TOP_SLOW_SPANS` | `5` | Number of slow spans in summaries |
| `OBESERVA_CAUSATION_ENABLED` | `true` | Attach causation graphs to summaries |
| `OBESERVA_SLOW_REQUEST_THRESHOLD_MS` | `1000` | Slow request threshold in milliseconds |

Summaries are available from `TraceSummaryRegistry` after each request. Enable span snapshot collection via summaries, debug toolbar, or Telescope.

Configuration: `config/obeserva.php` → `summaries.*`, `causation.*`.

See [v0.9.0 announcement](posts/v0.9.0-ai-advanced-features.md).

## Local development

```bash
git clone git@github.com:zaeem2396/laravel-obeserva.git
cd laravel-obeserva
composer install
composer ci
```
