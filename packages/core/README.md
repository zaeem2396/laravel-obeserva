# obeserva/core

Instrumentation runtime for Obeserva: tracers, spans, context, and sampling.

## Installation

```bash
composer require obeserva/core
```

Typically installed via `scout/laravel`.

## Components

- `Tracer` — span lifecycle, nesting, flush buffer, `trace()` scopes
- `Span`, `NoopSpan`, `SpanScope`
- `ContextManager` — trace context + active span stack
- `AlwaysOnSampler`, `ProbabilitySampler`

## Version

**0.2.0** — Span lifecycle engine with nested tracing.

## License

MIT
