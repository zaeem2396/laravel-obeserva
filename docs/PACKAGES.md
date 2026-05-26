# Packages

## Overview

| Directory | Composer name | Version | Role |
|-----------|---------------|---------|------|
| `packages/contracts` | `obeserva/contracts` | 0.1.0 | Interfaces and value objects |
| `packages/core` | `obeserva/core` | 0.1.0 | Runtime implementation |
| `packages/laravel` | `scout/laravel` | 0.1.0 | Laravel integration |
| `packages/scout-driver` | `obeserva/scout-driver` | 0.1.0 | Scout APM adapter (scaffold) |
| `packages/otel-driver` | `obeserva/otel-driver` | 0.1.0 | OTel exporter (scaffold) |
| `packages/testing` | `obeserva/testing` | 0.1.0 | Test doubles and assertions |

## Installation matrix

### End users (Laravel apps)

```bash
composer require scout/laravel
```

### Monorepo development

Path repositories are configured in the root `composer.json`:

```json
{
  "repositories": [
    { "type": "path", "url": "packages/*", "options": { "symlink": true } }
  ]
}
```

### Fresh Laravel app (path install)

```bash
composer config repositories.obeserva path "../path-to-monorepo/packages/*"
composer config minimum-stability dev
composer config prefer-stable true
composer require scout/laravel:@dev
```

## Package boundaries (v0.1.0)

### obeserva/contracts

- `SpanInterface`, `SpanKind`
- `TraceContextInterface`, `TraceContext`
- `TracerInterface`, `ContextStorageInterface`, `PropagationInterface`, `SamplerInterface`, `ExporterInterface`

No framework or driver dependencies.

### obeserva/core

- `Tracer`, `Span`, `NoopSpan`
- `ContextManager`
- `AlwaysOnSampler`, `ProbabilitySampler`

### scout/laravel

- `ObeservaServiceProvider`
- `TraceRequestMiddleware`
- `config/obeserva.php`
- `Obeserva` facade

Auto-discovered via Laravel package discovery.

### obeserva/scout-driver & obeserva/otel-driver

Scaffold adapters for v0.5.0 and v0.6.0 roadmap work. Not required for basic HTTP instrumentation in v0.1.0.

### obeserva/testing

- `FakeTracer` with `assertSpanRecorded()` and `recordedSpans()`

Dev dependency for package consumers writing tests.
