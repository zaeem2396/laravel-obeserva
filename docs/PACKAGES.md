# Modules

## Overview

| Directory | Namespace | Role |
|-----------|-----------|------|
| `src/Contracts` | `Obeserva\Contracts` | Interfaces and value objects |
| `src/Core` | `Obeserva\Core` | Runtime implementation |
| `src` | `Obeserva\Laravel` | Laravel integration |
| `src/Runtime` | `Obeserva\Laravel\Runtime` | Worker runtime detection and context isolation |
| `src/DeveloperExperience` | `Obeserva\DeveloperExperience` | Trace snapshots, Telescope publisher, debug toolbar |
| `src/FakeTracer.php` | `Obeserva\Testing` | Test double and assertion helpers |

### Testing utilities (v0.7.1)

- `FakeTracer` — in-memory tracer with span and snapshot assertions
- `TraceContextAssert` — W3C traceparent and queue payload propagation checks
- `TraceSnapshotBuilder` — fluent `TraceSnapshot` fixtures for tests
- `TraceSnapshotAssert` — snapshot count, attributes, hierarchy, and flow checks
- `InteractsWithObeserva` — Laravel Testbench trait to swap the tracer in package tests

### Distributed systems (v0.8.0)

- `TraceCarrierBag`, `W3cTracePropagator` — unified W3C trace carriers
- `PropagationContextResolver` — resolves active trace context and correlation IDs
- `TracePropagatingEventDispatcher`, `EventTraceContextCarrier`, `InteractsWithTraceContext` — event propagation
- `TraceNotificationListener` — notification send/sent spans
- `TraceBroadcastListener`, `BroadcastInstrumentation` — broadcast tracing and propagation
- `CorrelationContextStorage`, `IncomingCorrelationResolver`, `OutgoingCorrelationHeaders` — cross-service correlation

### Production engineering (v0.8.1)

- `CompletedSpanBufferPolicy`, `MemoryPressureMonitor` — bounded buffers and RSS-triggered auto-flush
- `TracerFlushGuard` — exception-safe tracer flush
- `ShutdownFlushRegistrar`, `FlushTracerOnWorkerStoppingListener`, `ProductionFlushSafety` — shutdown and worker-stop flush

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

### Worker context isolation

- `WorkerRuntime`, `WorkerRuntimeDetector`, `WorkerContextResetter`
- `WorkerContextIsolation`, `IsolateWorkerContextAfterJobListener`, `IsolateLongRunningWorkerContextListener`

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

Current version: **0.8.0**. A single package tag (`vX.Y.Z`) is published for each release. See [RELEASE.md](RELEASE.md) and [posts/v0.8.0-distributed-systems.md](posts/v0.8.0-distributed-systems.md).
