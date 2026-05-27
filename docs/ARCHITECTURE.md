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

## CI/CD

All quality gates run from the package root. See [CI.md](CI.md).
