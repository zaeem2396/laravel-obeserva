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
- **Drivers** — `TracerInterface`, `ContextStorageInterface`, `PropagationInterface`, `SamplerInterface`, `ExporterInterface`

## Version

**0.1.0** — Foundation release. Interfaces only; no runtime logic.

## License

MIT
