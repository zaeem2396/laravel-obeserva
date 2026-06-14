# Modules

## Overview

| Directory | Namespace | Role |
|-----------|-----------|------|
| `src/Contracts` | `Obeserva\Contracts` | Interfaces and value objects |
| `src/Core` | `Obeserva\Core` | Runtime implementation |
| `src` | `Obeserva\Laravel` | Laravel integration |
| `src/DeveloperExperience` | `Obeserva\DeveloperExperience` | Trace snapshots, Telescope publisher, debug toolbar |
| `src/FakeTracer.php` | `Obeserva\Testing` | Test double |

## Installation

```bash
composer require scout/laravel
```

### Cache instrumentation (v0.4.1)

- `TraceCacheEventListener` — cache hit/miss/write/forget spans
- `TraceRedisCommandExecutedListener` — Redis command spans

### Scout driver

- `ScoutSpanExporter` — span lifecycle export to Scout APM
- `ScoutSpanMapper`, `ScoutContextBridge`, `ScoutDriverFactory`
- `ScoutMetadataEnricher`, `ScoutSpanMetadataMapper`, `ScoutRuntimeDiagnostics`
- `ContainerScoutApmClient` — resolves `Scoutapm\ScoutApmAgent` from the container

### OpenTelemetry driver

- `OtelSpanExporter` — batched OTel span export
- `OtelSpanConverter`, `OtelSemanticConventionMapper`, `OtelSpanKindMapper`, `OtelSpanNameNormalizer`
- `OtelDriverFactory`, `LifecycleExporterResolver`

### Developer experience

- `TraceSnapshot`, `SpanSnapshotCollector`, `TraceSnapshotRegistry`
- `TraceTreeBuilder`, `PropagationFlowInspector`
- `DebugToolbarMiddleware`, `DebugToolbarRenderer`, `DebugToolbarDataBuilder`
- `PublishTraceToTelescope`, `TelescopeTraceEntryFactory`, `LaravelTelescopePublisher`
- `CompositeSpanLifecycleExporter`

### Horizon instrumentation (v0.4.0)

- `Horizon`, `HorizonInstrumentation`
- Supervisor/worker lifecycle listeners, throughput metrics, retry correlator

### Queue instrumentation (v0.3.1)

- `TraceContextCarrier`, `QueuePayloadHook`
- Job processing/processed/failed listeners

### Database instrumentation (v0.3.0)

- `TraceQueryListener`, `QuerySanitizer`, `NPlusOneDetector`

## Releases

Current version: **0.7.0**. A single package tag (`vX.Y.Z`) is published for each release. See [RELEASE.md](RELEASE.md) and [posts/v0.7.0-developer-experience.md](posts/v0.7.0-developer-experience.md).
