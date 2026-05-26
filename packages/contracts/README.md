# obeserva/contracts

Vendor-neutral observability contracts for the Obeserva Laravel instrumentation runtime.

## Installation

```bash
composer require obeserva/contracts
```

This package is typically installed transitively via `scout/laravel`.

## Contents

- **Span** — `SpanInterface`, `SpanKind`
- **Trace** — `TraceContextInterface`, `TraceContext` (W3C `traceparent`)
- **Drivers** — `TracerInterface`, `ContextStorageInterface`, `ActiveSpanStorageInterface`, `PropagationInterface`, `SamplerInterface`, `ExporterInterface`
- **Identifiers** — `SpanIds` (W3C-compatible trace/span id generation)

## Version

**0.3.1** — Stable contracts for span lifecycle, trace context, and driver interfaces (monorepo release; no API changes in this patch).

## License

MIT
