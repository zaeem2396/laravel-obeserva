# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/zaeem2396/laravel-obeserva/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/zaeem2396/laravel-obeserva/releases/tag/v0.1.0
