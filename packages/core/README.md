# obeserva/core

Low-overhead instrumentation runtime for Obeserva.

## Installation

```bash
composer require obeserva/core
```

Requires `obeserva/contracts`.

## Contents (v0.1.0)

- `Tracer` — span creation with sampling
- `Span`, `NoopSpan` — span lifecycle
- `ContextManager` — trace context storage
- `AlwaysOnSampler`, `ProbabilitySampler`

## Version

**0.1.0** — Foundation scaffold. Nesting, flush pipelines, and export batching evolve in v0.2.x+.

## License

MIT
