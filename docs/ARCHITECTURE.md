# Architecture

Obeserva is a **modular monorepo** for Laravel observability. Each package has a single responsibility and strict dependency boundaries.

## Design principles

1. **Vendor neutrality** — Application code depends on `obeserva/contracts`, not Scout or OpenTelemetry directly.
2. **Dependency inversion** — Drivers implement contract interfaces; Laravel integration consumes the core runtime.
3. **Low overhead** — Spans use noop paths when sampling rejects a trace; hot paths avoid unnecessary allocation.
4. **Runtime awareness** — Context storage is designed for HTTP, queue, Horizon, Octane, and CLI (full support rolls out across roadmap versions).

## Layer diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                       │
│              composer require scout/laravel                    │
└────────────────────────────┬────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────┐
│  scout/laravel (Obeserva\Laravel)                            │
│  Service provider · HTTP/DB listeners · config · facades    │
└────────────────────────────┬────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────┐
│  obeserva/core (Obeserva\Core)                               │
│  Tracer · Span lifecycle · ContextManager · Sampling         │
└────────────────────────────┬────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────┐
│  obeserva/contracts (Obeserva\Contracts)                     │
│  SpanInterface · TraceContext · TracerInterface · Drivers    │
└────────────────────────────┬────────────────────────────────┘
                             │
         ┌───────────────────┴───────────────────┐
         ▼                                       ▼
┌─────────────────┐                   ┌─────────────────┐
│ obeserva/       │                   │ obeserva/       │
│ scout-driver    │                   │ otel-driver     │
│ (Scout APM)     │                   │ (OpenTelemetry) │
└─────────────────┘                   └─────────────────┘
```

## Dependency rules

| Package | May depend on |
|---------|----------------|
| `obeserva/contracts` | PHP only |
| `obeserva/core` | `contracts` |
| `scout/laravel` | `contracts`, `core`, `illuminate/*` |
| `obeserva/scout-driver` | `contracts`, `core` |
| `obeserva/otel-driver` | `contracts`, `core` |
| `obeserva/testing` | `contracts`, `core`, `phpunit` |

**Forbidden:** `contracts` must not depend on any other Obeserva package. Drivers must not depend on `scout/laravel`.

## Trace propagation

**W3C Trace Context** `traceparent` headers are supported via `TraceContext::toPropagationHeaders()` and `fromPropagationHeaders()`.

### Span lifecycle (v0.2.0)

The `Tracer` coordinates span creation with `ContextManager`:

1. Resolve `trace_id` from incoming context or generate a new one (`SpanIds`)
2. Resolve `parent_span_id` from the active span stack or incoming context
3. Push the new span onto the active stack and update trace context
4. On `end()`, pop the stack and move the span into the completed-span buffer for `flush()`

Use `Tracer::trace()` / `SpanScope` when you want automatic span completion at the end of a scope.

Queue and event propagation are planned for v0.3.1–v0.8.x.

### Database instrumentation (v0.3.0)

Laravel database observability hooks into `Illuminate\Database\Events\QueryExecuted`:

1. `TraceQueryListener` creates a child `db.{operation}` span per query
2. `QuerySanitizer` redacts bindings into a safe `db.statement` attribute
3. `QueryCounter` increments `db.query_count` on the active request span
4. `NPlusOneDetector` flags repeated query patterns and annotates the request span

Configuration lives under `config/obeserva.php` → `database.*` (see [INSTALLATION.md](INSTALLATION.md)).

### Queue instrumentation (v0.3.1)

1. `QueuePayloadHook` registers with `Queue::createPayloadUsing` to embed trace context in payloads
2. `TraceJobProcessingListener` restores context and opens a `queue.process:*` consumer span
3. `TraceJobProcessedListener` / `TraceJobFailedListener` close spans and clear worker context
4. Works across queue drivers that use Laravel's standard payload format (sync, Redis, database, SQS)

## CI/CD

All packages are validated together from the monorepo root. See [CI.md](CI.md).
