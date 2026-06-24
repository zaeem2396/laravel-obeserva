# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Nothing yet.

### Changed

- Nothing yet.

## [1.0.0] - 2026-06-03

### Added

- Production stabilization: `PackageVersion` constant (`1.0.0`) for diagnostics and Scout metadata
- `RuntimeDiagnostics`, `RuntimeDiagnosticsBuilder`, and `php artisan obeserva:status` command with `--json` output
- `ConfigValidator` with optional strict boot validation via `OBESERVA_CONFIG_STRICT`
- Scout export tag `scout.obeserva.version` via `ScoutMetadataEnricher`
- Octane isolation integration test coverage
- Documentation: upgrade guide, API stability policy, Scout integration, benchmarks
- CI: docs validation workflow (`scripts/validate-docs-examples.php`)
- Tests: stability diagnostics, config validation, status command, Octane isolation (120 tests total)

### Changed

- Documentation aligned for v1.0.0 release (installation, roadmap, architecture, release post)

## [0.9.0] - 2026-06-02

### Added

- AI/advanced features: `TraceSummary` and `TraceSummaryBuilder` for structured, LLM-friendly trace summaries
- `SpanCategoryResolver` categorizes spans (HTTP, database, cache, queue, events, and more)
- `CausationGraph`, `CausationGraphBuilder`, and `SlowRequestAnalyzer` for slow-request root-cause attribution
- `TraceSummaryJsonFormatter` exports summaries as JSON for debugging workflows
- `BuildTraceSummaryOnTerminate` stores per-request summaries in `TraceSummaryRegistry`
- `TraceSummaryAssert` testing helper and `obeservaTraceSummary()` on `InteractsWithObeserva`
- Debug toolbar and Telescope entries include `trace_summary` when summaries are enabled
- Config: `obeserva.summaries.*`, `obeserva.causation.*`
- Tests: summary builder, causation graph, slow request analysis, terminate integration (110 tests total)

### Changed

- `LifecycleExporterResolver` collects span snapshots when `OBESERVA_TRACE_SUMMARIES` is enabled
- Documentation aligned for v0.9.0 release (installation, roadmap, architecture, release post)

### Fixed

- `OBESERVA_SUMMARY_TOP_SLOW_SPANS=0` now returns empty top-slow and root-cause lists instead of forcing one span

## [0.8.1] - 2026-06-01

### Added

- Production engineering: `CompletedSpanBufferPolicy` auto-flushes when completed span buffer exceeds `OBESERVA_MAX_COMPLETED_SPANS`
- `MemoryPressureMonitor` triggers tracer flush when RSS exceeds `OBESERVA_MEMORY_PRESSURE_BYTES`
- Bounded `TraceSnapshotRegistry` evicts oldest snapshots at `OBESERVA_MAX_TRACE_SNAPSHOTS`
- Active span depth guard on `ContextManager` (`OBESERVA_MAX_ACTIVE_SPAN_DEPTH`) ends orphaned spans when stack overflows
- `TracerFlushGuard` swallows export exceptions so flush failures never break the application
- `ShutdownFlushRegistrar` and `FlushTracerOnWorkerStoppingListener` for safe flush on shutdown and worker stop
- Config: `obeserva.memory.*`, `obeserva.flush.*`
- Tests: memory bounds, flush guard, shutdown and worker-stopping listeners (108 tests total)

### Changed

- `WorkerContextResetter` flushes via `TracerFlushGuard` instead of calling the tracer directly
- Documentation aligned for v0.8.1 release (installation, roadmap, architecture, release post)

## [0.8.0] - 2026-05-31

### Added

- Distributed systems module: unified `TraceCarrierBag` and `W3cTracePropagator` for W3C trace context carriers
- `PropagationContextResolver` shared across queue, events, and broadcasts
- Cross-service correlation via `CorrelationContextStorage`, incoming `X-Correlation-ID` resolution, and outbound response headers
- Event propagation: `TracePropagatingEventDispatcher`, `EventTraceContextCarrier`, and `InteractsWithTraceContext` trait
- Notification tracing spans (`notification.send` / `notification.sent`) via `TraceNotificationListener`
- Broadcast tracing and propagation via `TraceBroadcastListener` and `BroadcastInstrumentation`
- Config: `obeserva.events.*`, `obeserva.notifications.*`, `obeserva.broadcasts.*`, `obeserva.correlation.*`
- Tests: propagation, correlation, and event integration coverage (98 tests total)

### Changed

- `TraceContextCarrier` and `QueuePayloadHook` delegate to shared propagation helpers and include correlation IDs
- HTTP middleware enriches spans with `correlation.id` and echoes correlation headers on responses
- Worker context reset clears correlation storage between jobs
- Documentation aligned for v0.8.0 release (installation, roadmap, architecture, release post)

### Security

- Updated `guzzlehttp/guzzle` to 7.12.3 and `guzzlehttp/psr7` to 2.12.3 (CVE-2026-55568, CVE-2026-55766, CVE-2026-55767)

## [0.7.1] - 2026-05-30

### Added

- Testing utilities (`Obeserva\Testing`): `TraceContextAssert` for W3C traceparent and queue payload propagation checks
- `TraceSnapshotBuilder` and `TraceSnapshotAssert` for snapshot fixtures, hierarchy, attributes, and flow assertions
- Extended `FakeTracer` with `findSpan()`, span count/attribute/child assertions, and `spanSnapshots()`
- `InteractsWithObeserva` trait for Laravel Testbench package tests
- `scripts/benchmark-instrumentation.php` — flat spans, nested span trees, and snapshot conversion benchmarks
- Tests: testing utilities unit and Laravel integration coverage (92 tests total)

### Changed

- Benchmark CI workflow runs `scripts/benchmark-instrumentation.php` instead of an inline placeholder
- Documentation aligned for v0.7.1 release (installation, roadmap, architecture, release post)

## [0.7.0] - 2026-05-29

### Added

- Developer experience module (`Obeserva\DeveloperExperience`) for local trace inspection
- Telescope integration (`OBESERVA_TELESCOPE_ENABLED`) publishes span snapshots to Laravel Telescope on terminate
- Debug toolbar (`OBESERVA_DEBUG_TOOLBAR`) injects a local HTML trace panel into HTML responses
- `CompositeSpanLifecycleExporter` chains primary driver export with development snapshot collection
- `TraceSnapshot`, `TraceTreeBuilder`, and `PropagationFlowInspector` for hierarchical trace views
- `SpanSnapshotCollector` records completed spans into `TraceSnapshotRegistry` during development
- Config: `obeserva.development.*` and env vars `OBESERVA_TELESCOPE_*`, `OBESERVA_DEBUG_TOOLBAR*`
- Tests: developer experience unit and Laravel integration coverage (85 tests total)

### Changed

- Documentation aligned for v0.7.0 release (installation, roadmap, architecture, release post)

## [0.6.1] - 2026-05-28

### Added

- Worker context isolation for dedicated queue and Horizon workers via `WorkerContextResetter`
- `WorkerRuntimeDetector` identifies HTTP, queue worker, Octane, and RoadRunner runtimes
- `IsolateWorkerContextAfterJobListener` flushes tracer state after each job in long-running workers
- Optional Octane and RoadRunner request/task termination hooks for context cleanup
- Config: `obeserva.worker.*` and env vars `OBESERVA_WORKER_*`, `OBESERVA_OCTANE_ISOLATION`, `OBESERVA_ROADRUNNER_ISOLATION`
- Tests: worker runtime unit and integration coverage (85 tests total)

### Changed

- Documentation aligned for v0.6.1 release (installation, roadmap, architecture, release post)

## [0.6.0] - 2026-06-14

### Added

- OpenTelemetry driver (`OBESERVA_DRIVER=otel`) with batched span export via `OtelSpanExporter`
- `OtelSemanticConventionMapper` aligns HTTP, database, queue, and cache attributes to OTel semantic conventions
- `OtelSpanConverter`, `OtelSpanNameNormalizer`, and `OtelSpanKindMapper` for OTel-compatible payloads
- `LifecycleExporterResolver` selects Scout, OTel, or noop lifecycle exporters by driver config
- Config: `obeserva.otel.*` and env vars `OBESERVA_OTEL_*`
- `SpanInterface` exposes `getStartedAt()` and `getEndedAt()` for driver export timestamps
- Tests: OTel driver unit and Laravel integration coverage (70 tests total)

### Changed

- Documentation aligned for v0.6.0 release (installation, roadmap, architecture, release post)

## [0.5.1] - 2026-06-14

### Added

- Advanced Scout metadata enrichment (`ScoutMetadataEnricher`) — route names, queue names, Horizon worker IDs, deployment version, tenant ID, and runtime diagnostics exported as `scout.*` tags
- `ScoutSpanMetadataMapper` maps Laravel span attributes to Scout-prefixed metadata keys
- `ScoutRuntimeDiagnostics` captures PHP/Laravel version and environment tags on export
- Config: `obeserva.scout.deployment_version`, `tenant_id`, `metadata_enabled`
- Environment variables: `OBESERVA_SCOUT_DEPLOYMENT_VERSION`, `OBESERVA_SCOUT_TENANT_ID`, `OBESERVA_SCOUT_METADATA_ENABLED`
- Tests: metadata mapper, enricher, diagnostics, and integration coverage (60 tests total)

### Changed

- Documentation aligned for v0.5.1 release (installation, roadmap, architecture, release post)
- Package install CI: Laravel 11 smoke test workaround for CVE-2026-48019 advisory blocking

## [0.5.0] - 2026-05-28

### Added

- Scout APM driver (`OBESERVA_DRIVER=scout`) with span lifecycle export via `ScoutSpanExporter`
- `SpanLifecycleExporterInterface` and core tracer hooks for driver export on span start/end/flush
- `ScoutSpanMapper`, `ScoutContextBridge`, `ScoutApmClientInterface`, and `ContainerScoutApmClient` adapter
- Scout configuration: `obeserva.scout.*` and env vars `OBESERVA_SCOUT_*`
- Tests: scout driver unit and Laravel integration coverage (51 tests total)

### Changed

- `SpanInterface` exposes `getKind()` and `getAttributes()` for driver mapping
- Documentation aligned for v0.5.0 release (installation, roadmap, architecture, release post)

## [0.4.1] - 2026-05-28

### Added

- Cache instrumentation via `TraceCacheEventListener` — `cache.get`, `cache.miss`, `cache.put`, `cache.forget` spans with store/key metadata
- Redis command instrumentation via `TraceRedisCommandExecutedListener` — `redis.{command}` spans from `CommandExecuted` events
- Config: `obeserva.cache.enabled`, `obeserva.redis.command_tracing`
- Environment variables: `OBESERVA_CACHE_ENABLED`, `OBESERVA_REDIS_COMMAND_TRACING`
- Tests: cache event listener and Redis command listener coverage

### Changed

- Consolidated repository from multi-package monorepo to a single `scout/laravel` package layout
- README reorganized with table of contents and documentation index
- Documentation aligned for v0.4.1 release (installation, roadmap, architecture, release post)

## [0.4.0] - 2026-05-26

### Added

- Optional Horizon instrumentation (auto-detected when `laravel/horizon` is installed)
- `HorizonInstrumentation` — registers Horizon event listeners without hard dependency
- `TraceHorizonSupervisorLoopedListener` — `horizon.supervisor:*` spans with throughput attributes
- `TraceHorizonWorkerProcessRestartingListener` and `TraceHorizonSupervisorProcessRestartingListener`
- `TraceHorizonJobReservedListener` / `TraceHorizonJobReleasedListener` — throughput metrics
- `HorizonThroughputMetrics`, `ActiveHorizonSupervisorRegistry`, `HorizonJobPayloadReader`
- `HorizonRetryCorrelator` — `root_trace_id` and `queue.retry_attempt` on job spans
- Config: `obeserva.horizon.*` environment variables
- `laravel/horizon` suggested dependency for `scout/laravel`
- Tests: metrics, retry correlator, payload reader, supervisor loop listener

### Changed

- `TraceContextCarrier::inject()` stores `root_trace_id` for retry causation chains
- `JobSpanEnricher` adds Horizon job metadata and retry correlation attributes
- `FlushTracerOnTerminate` clears Horizon supervisor registry and metrics
- Documentation: package READMEs, ROADMAP, CI, architecture, and release posts aligned for v0.4.0

## [0.3.1] - 2026-05-26

### Added

- `TraceContextCarrier` — inject/extract W3C trace context in queue job payloads
- `QueuePayloadHook` — registers `Queue::createPayloadUsing` for automatic propagation
- `TraceJobProcessingListener` — consumer spans (`queue.process:{job}`) with queue metadata
- `TraceJobProcessedListener` and `TraceJobFailedListener` — success/failure lifecycle and exception correlation
- `ActiveJobSpanRegistry` and `JobSpanEnricher` — job span tracking and attributes
- Config: `obeserva.queue.job_tracing`, `obeserva.queue.failed_job_correlation`
- `illuminate/queue` dependency for `scout/laravel`
- Tests: carrier round-trip, payload hook, sync queue propagation

### Changed

- `FlushTracerOnTerminate` clears active job span registry per request/worker cycle

## [0.3.0] - 2026-05-26

### Added

- `TraceQueryListener` — child `db.{operation}` spans for every `QueryExecuted` event
- `QuerySanitizer` — binding-aware SQL redaction with statement length limits
- `QueryOperation` — classifies SQL into select/insert/update/delete operations
- `QueryCounter` — tracks `db.query_count` on the active request span
- `NPlusOneDetector` and `NPlusOneDetectionListener` — flags repeated query patterns (lazy-load / N+1 hook)
- Config: `obeserva.database.query_tracing`, `obeserva.database.lazy_loading_detection`
- `illuminate/database` dependency for `scout/laravel`
- Tests: sanitizer, operation parser, query listener, N+1 detection

### Changed

- `FlushTracerOnTerminate` resets query and N+1 counters per request

## [0.2.1] - 2026-05-26

### Added

- `RequestSpanEnricher` — centralized HTTP, route, response, user, and exception attribute enrichment
- `RouteMatchedListener` — route action, middleware stack, and `route.matched` span events
- `ReportExceptionListener` — correlates reported exceptions to the active request span or creates an `exception` span
- `TraceMiddlewareTiming` — `obeserva.timing:{name}` middleware alias for pipeline segment child spans
- `FlushTracerOnTerminate` — clears completed spans at end of request lifecycle (configurable)
- Config: `obeserva.exceptions.enabled`, `obeserva.terminate.flush_tracer`, `obeserva.http.middleware_timing_alias`
- HTTP attributes: `http.scheme`, `http.host`, `http.client_ip`, `http.user_agent`, response content type/length
- Span events: `request.received`, `response.sent`, `exception`

### Changed

- `TraceRequestMiddleware` delegates enrichment to `RequestSpanEnricher`
- Exception handler hooks via Laravel `reportable()` when using the foundation exception handler

## [0.2.0] - 2026-05-26

### Added

- `ActiveSpanStorageInterface` for nested span tracking
- `SpanIds` utility for W3C-compatible trace and span identifiers
- Extended `SpanInterface` with trace identity, duration, and `isEnded()`
- Span lifecycle engine: parent-child nesting, active span stack, completed-span flush buffer
- `SpanScope` for automatic span completion via RAII-style scopes
- `Tracer::trace()` helper and `completedSpans()` for inspection and export hooks
- HTTP middleware: request duration, route name, full URL, authenticated user id, exception metadata
- Laravel service provider: request-scoped `ContextManager` wired into `Tracer`, deferred middleware registration
- Tests: span lifecycle, tracer nesting, context manager, middleware integration, `SpanIds`

### Changed

- `ContextManager` implements active span stack and `clear()` resets trace + spans
- `FakeTracer` records nested spans with shared trace ids
- `Span::toArray()` includes trace metadata and duration

## [0.1.0] - 2026-05-26

### Added

- Modular monorepo with strict package boundaries under `packages/`
- `obeserva/contracts` — vendor-neutral span, trace context, and driver interfaces
- `obeserva/core` — instrumentation runtime scaffold (tracer, spans, context, sampling)
- `scout/laravel` — Laravel service provider, configuration, and HTTP middleware scaffold
- `obeserva/scout-driver` — Scout APM adapter package scaffold
- `obeserva/otel-driver` — OpenTelemetry exporter package scaffold
- `obeserva/testing` — `FakeTracer` and span assertion utilities
- GitHub Actions CI: PHPStan, Pint, PHPUnit, compatibility matrix (Laravel 11–13, PHP 8.3–8.5)
- GitHub Actions CI: Composer validate, security audit, coverage, infection, rector, benchmarks
- GitHub Actions CI: fresh Laravel package install validation
- Root development tooling: PHPUnit, PHPStan (max), Pint, Rector, Infection
- `composer pre-push` and `composer ci` scripts for local quality gates

### Requirements

- PHP ^8.3
- Laravel ^11.0 \| ^12.0 \| ^13.0 (for `scout/laravel`)

[Unreleased]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.7.0...HEAD
[0.7.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.6.1...v0.7.0
[0.6.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.5.1...v0.6.0
[0.5.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.4.1...v0.5.0
[0.4.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/zaeem2396/laravel-obeserva/releases/tag/v0.4.0
[0.3.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/zaeem2396/laravel-obeserva/releases/tag/v0.1.0
