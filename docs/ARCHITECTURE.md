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
- `Obeserva\Testing` provides test doubles, propagation/snapshot assertions, and can depend on runtime layers.

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

### Worker context isolation

Dedicated queue and Horizon workers flush tracer state after each job via `WorkerContextResetter`. Octane and RoadRunner runtimes register optional termination listeners when present.

### Developer experience

Local trace inspection runs alongside the configured driver:

1. `SpanSnapshotCollector` records `TraceSnapshot` values on span end into `TraceSnapshotRegistry`
2. `TraceTreeBuilder` and `PropagationFlowInspector` build hierarchical views and queue/HTTP propagation summaries
3. `DebugToolbarMiddleware` injects an HTML panel into HTML responses when `OBESERVA_DEBUG_TOOLBAR` is enabled
4. `PublishTraceToTelescope` publishes span snapshots to Laravel Telescope on terminate when `OBESERVA_TELESCOPE_ENABLED` is enabled

Requires optional `laravel/telescope` for Telescope publishing; the debug toolbar has no extra dependencies.

### Testing utilities

`Obeserva\Testing` ships PHPUnit-friendly helpers for package and application tests:

1. `FakeTracer` records spans in memory with assertion helpers and `spanSnapshots()`
2. `TraceContextAssert` validates W3C traceparent headers and queue payload propagation
3. `TraceSnapshotBuilder` and `TraceSnapshotAssert` build and verify snapshot hierarchies and flows
4. `InteractsWithObeserva` swaps the tracer during Orchestra Testbench tests

### Distributed systems (v0.8.0)

Trace continuity extends beyond queue jobs into Laravel's async surfaces:

1. `TraceCarrierBag` / `W3cTracePropagator` unify W3C carriers across queue, events, and broadcasts
2. `TracePropagatingEventDispatcher` injects context and records `event.dispatch` spans for application events
3. `TraceNotificationListener` and `TraceBroadcastListener` trace notification and broadcast workflows
4. `CorrelationContextStorage` resolves and propagates `X-Correlation-ID` across HTTP, queue, and span attributes

Application events opt in via `InteractsWithTraceContext`. Framework events under `Illuminate\*` are not instrumented.

### Production engineering (v0.8.1)

Long-running worker safety layers sit below instrumentation:

1. `CompletedSpanBufferPolicy` and `MemoryPressureMonitor` auto-flush the tracer when buffers or RSS thresholds are exceeded
2. `ContextManager` ends orphaned spans when active nesting exceeds `OBESERVA_MAX_ACTIVE_SPAN_DEPTH`
3. `TraceSnapshotRegistry` evicts oldest snapshots when development inspection exceeds `OBESERVA_MAX_TRACE_SNAPSHOTS`
4. `TracerFlushGuard` ensures export failures never propagate to application code
5. `ShutdownFlushRegistrar` and `FlushTracerOnWorkerStoppingListener` flush on PHP shutdown and queue worker stop events

`WorkerContextResetter` routes all worker-cycle flushes through `TracerFlushGuard`.

### AI/advanced features (v0.9.0)

Structured debugging and causation layers sit above span snapshots:

1. `SpanCategoryResolver` classifies spans into HTTP, database, cache, queue, event, and other categories
2. `TraceSummaryBuilder` produces compact summaries with category counts, top slow spans, and propagation metadata
3. `SlowRequestAnalyzer` flags slow HTTP requests and ranks root-cause child spans
4. `CausationGraphBuilder` emits parent-child graphs with `root_cause_span_ids`
5. `BuildTraceSummaryOnTerminate` stores summaries in `TraceSummaryRegistry` for toolbar, Telescope, and tests

`TraceSummaryJsonFormatter` exports summaries as JSON for external tooling and LLM workflows.

### Stable release (v1.0.0)

Production stabilization and operational diagnostics:

1. `PackageVersion` exposes the semver package version for diagnostics and Scout metadata
2. `RuntimeDiagnosticsBuilder` snapshots driver, features, memory bounds, and worker runtime
3. `ConfigValidator` validates driver and sampling configuration at boot (optional strict mode)
4. `ObeservaStatusCommand` (`php artisan obeserva:status`) surfaces runtime diagnostics in the console or JSON

See [API stability](API_STABILITY.md) and [upgrade guide](UPGRADE.md) for 1.x semver guarantees.

## CI/CD

All quality gates run from the package root. See [CI.md](CI.md).
