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
│  Service provider · HTTP middleware · config · facades       │
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

Queue, event, and cross-service propagation are planned for v0.3.x–v0.8.x.

## CI/CD

All packages are validated together from the monorepo root. See [CI.md](CI.md).
