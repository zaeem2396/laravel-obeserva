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

## Trace propagation (v0.1.0)

v0.1.0 establishes the **W3C Trace Context** `traceparent` header format via `TraceContext::toPropagationHeaders()` and `fromPropagationHeaders()`. Queue, event, and cross-service propagation are planned for v0.3.x–v0.8.x.

## CI/CD

All packages are validated together from the monorepo root. See [CI.md](CI.md).
