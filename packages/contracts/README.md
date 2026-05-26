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

**0.2.0** — Active span storage interface and extended span contract.

## License

MIT
