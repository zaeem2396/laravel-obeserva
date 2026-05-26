# Packages

## Overview

| Directory | Composer name | Version | Role |
|-----------|---------------|---------|------|
| `packages/contracts` | `obeserva/contracts` | 0.2.1 | Interfaces and value objects |
| `packages/core` | `obeserva/core` | 0.2.1 | Runtime implementation |
| `packages/laravel` | `scout/laravel` | 0.2.1 | Laravel integration |
| `packages/scout-driver` | `obeserva/scout-driver` | 0.2.1 | Scout APM adapter (scaffold) |
| `packages/otel-driver` | `obeserva/otel-driver` | 0.2.1 | OTel exporter (scaffold) |
| `packages/testing` | `obeserva/testing` | 0.2.1 | Test doubles and assertions |

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

## Package boundaries (v0.2.1)

### obeserva/contracts

- `SpanInterface`, `SpanKind`
- `TraceContextInterface`, `TraceContext`, `SpanIds`
- `TracerInterface`, `ContextStorageInterface`, `ActiveSpanStorageInterface`
- `PropagationInterface`, `SamplerInterface`, `ExporterInterface`

No framework or driver dependencies.

### obeserva/core

- `Tracer` (nesting, flush buffer, `trace()` scopes)
- `Span`, `NoopSpan`, `SpanScope`
- `ContextManager` (trace context + active span stack)
- `AlwaysOnSampler`, `ProbabilitySampler`

### scout/laravel

- `ObeservaServiceProvider` (listeners, exception hooks, terminate flush)
- `TraceRequestMiddleware`, `RequestSpanEnricher`
- `TraceMiddlewareTiming` (`obeserva.timing:{segment}` alias)
- `RouteMatchedListener`, `ReportExceptionListener`
- `config/obeserva.php`
- `Obeserva` facade (`startSpan`, `trace`)

Auto-discovered via Laravel package discovery.

### obeserva/scout-driver & obeserva/otel-driver

Scaffold adapters for v0.5.0 and v0.6.0 roadmap work. Not required for basic HTTP instrumentation in v0.1.0.

### obeserva/testing

- `FakeTracer` with `assertSpanRecorded()` and `recordedSpans()`

Dev dependency for package consumers writing tests.

## Releases

All packages share the monorepo version (currently **0.2.1**). Release process, tagging, and announcements: [RELEASE.md](RELEASE.md), [posts/v0.2.1-laravel-http.md](posts/v0.2.1-laravel-http.md).
