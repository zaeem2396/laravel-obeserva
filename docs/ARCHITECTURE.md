# Architecture

Obeserva is a single Composer package (`scout/laravel`) with clear internal runtime layers.

## Layer diagram

```
Laravel Application
      |
Obeserva\Laravel (service provider, middleware, listeners)
      |
Obeserva\Core (tracer, spans, context, sampling)
      |
Obeserva\Contracts (interfaces and value objects)
```

## Internal module boundaries

- `Obeserva\Contracts` depends only on PHP.
- `Obeserva\Core` depends on `Obeserva\Contracts`.
- `Obeserva\Laravel` depends on `Obeserva\Core`, `Obeserva\Contracts`, and `illuminate/*`.
- `Obeserva\Testing` provides test doubles and can depend on runtime layers.

## Trace propagation

W3C Trace Context `traceparent` headers are supported via `TraceContext::toPropagationHeaders()` and `fromPropagationHeaders()`.

### Cache instrumentation (v0.4.1)

Laravel cache events drive client spans via `TraceCacheEventListener`:

1. `CacheHit` / `CacheMissed` → `cache.get` / `cache.miss` with `cache.hit` attribute
2. `KeyWritten` → `cache.put` with optional `cache.ttl_seconds`
3. `KeyForgotten` → `cache.forget`

Configuration: `config/obeserva.php` → `cache.*` (see [INSTALLATION.md](INSTALLATION.md)).

### Redis instrumentation (v0.4.1)

`TraceRedisCommandExecutedListener` listens to `CommandExecuted` and records `redis.{command}` spans with connection name and duration.

Configuration: `config/obeserva.php` → `redis.*` (see [INSTALLATION.md](INSTALLATION.md)).

### Scout driver

When `OBESERVA_DRIVER=scout`, completed spans are forwarded to Scout APM in real time:

1. `ScoutSpanMapper` maps Obeserva span kinds to Scout operation names (`HTTP/*`, `External/*`, `Job/*`, etc.)
2. `ScoutContextBridge` applies default tags and propagates trace/span metadata
3. `ScoutSpanExporter` calls the Scout agent on span start/end and `send()` on flush
4. `ScoutMetadataEnricher` promotes Laravel span attributes plus deployment/tenant/runtime tags to Scout context and request tags

Requires optional `scoutapp/scout-apm-laravel` and a bound `Scoutapm\ScoutApmAgent`.

### OpenTelemetry driver

When `OBESERVA_DRIVER=otel`, completed spans are converted to OTel-compatible payloads and exported on flush:

1. `OtelSemanticConventionMapper` normalizes attributes (`http.method` → `http.request.method`, `queue.name` → `messaging.destination.name`, etc.)
2. `OtelSpanNameNormalizer` produces convention-friendly span names (`GET users.index`, `process App\Jobs\SendEmail`)
3. `OtelSpanConverter` builds export payloads with trace/span IDs, timestamps, and resource attributes
4. `OtelSpanExporter` batches spans and calls `OtelExporterClientInterface::export()` on flush

`LifecycleExporterResolver` selects Scout, OTel, or noop exporters based on `obeserva.driver`. When development features are enabled, a `CompositeSpanLifecycleExporter` also runs `SpanSnapshotCollector` for local inspection.

### Developer experience

Local trace inspection runs alongside the configured driver:

1. `SpanSnapshotCollector` records `TraceSnapshot` values on span end into `TraceSnapshotRegistry`
2. `TraceTreeBuilder` and `PropagationFlowInspector` build hierarchical views and queue/HTTP propagation summaries
3. `DebugToolbarMiddleware` injects an HTML panel into HTML responses when `OBESERVA_DEBUG_TOOLBAR` is enabled
4. `PublishTraceToTelescope` publishes span snapshots to Laravel Telescope on terminate when `OBESERVA_TELESCOPE_ENABLED` is enabled

Requires optional `laravel/telescope` for Telescope publishing; the debug toolbar has no extra dependencies.

## CI/CD

All quality gates run from the package root. See [CI.md](CI.md).
