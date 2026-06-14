# Laravel Observability Runtime — Roadmap

**ChatGPT reference:** https://chatgpt.com/share/6a1020e7-cde4-83a2-a351-07e65da40e5f

**Install:** `composer require scout/laravel`

**Current release:** `v0.7.0` (2026-05-29) — see [CHANGELOG.md](CHANGELOG.md) and [docs/posts/v0.7.0-developer-experience.md](docs/posts/v0.7.0-developer-experience.md).

---

## Release Progress

| Version | Theme | Status | Tags |
|---------|-------|--------|------|
| v0.1.0 | Foundation (monorepo, CI) | 🟢 Released | `v0.1.0` |
| v0.1.1 | Core contracts | 🟢 Released (bundled in v0.1.0+) | — |
| v0.2.0 | Core runtime | 🟢 Released (all v0.2.0 rows ✅) | `v0.2.0` |
| v0.2.1 | Laravel HTTP | 🟢 Released (all v0.2.1 rows ✅) | `v0.2.1` |
| v0.3.0 | Database instrumentation | 🟢 Released (all v0.3.0 rows ✅) | `v0.3.0` |
| v0.3.1 | Queue instrumentation | 🟢 Released (all v0.3.1 rows ✅) | `v0.3.1` |
| v0.4.0 | Horizon integration | 🟢 Released (all v0.4.0 rows ✅) | `v0.4.0` |
| v0.4.1 | Cache instrumentation | 🟢 Released (all v0.4.1 rows ✅) | `v0.4.1` |
| v0.5.0 | Scout driver | 🟢 Released (all v0.5.0 rows ✅) | `v0.5.0` |
| v0.5.1 | Advanced Scout metadata | 🟢 Released (all v0.5.1 rows ✅) | `v0.5.1` |
| v0.6.0 | OpenTelemetry alignment | 🟢 Released (all v0.6.0 rows ✅) | `v0.6.0` |
| v0.6.1 | Worker context isolation | 🟢 Released (all v0.6.1 rows ✅) | `v0.6.1` |
| v0.7.0 | Developer experience | 🟢 Released (all v0.7.0 rows ✅) | `v0.7.0` |

---

## Project Vision

Build a modern Laravel-native observability and instrumentation layer designed around:

- deep Laravel runtime awareness
- vendor-neutral architecture
- low-overhead production tracing
- OpenTelemetry-aligned abstractions
- advanced queue and Horizon instrumentation
- Octane/RoadRunner compatibility

The primary goal is to create an instrumentation architecture sophisticated enough to impress the Scout APM engineering team and position the project as next-generation Laravel observability infrastructure.

---

## Status Labels

| Label | Meaning |
|-------|---------|
| 🟢 DONE | Completed and shipped (or fully delivered for that version row) |
| 🟡 IN-PROGRESS | Actively being built on an **unreleased** version |
| 🔴 PLANNED | Scheduled for a future version, not started |

---

## Required CI/CD Pipelines

| Pipeline | Purpose | Priority | Status |
|----------|---------|----------|--------|
| PHPStan / Larastan | Static analysis with max strictness | Critical | 🟢 DONE |
| Laravel Pint | Code style enforcement | Critical | 🟢 DONE |
| PHPUnit / Pest | Unit + integration testing | Critical | 🟢 DONE |
| Infection PHP | Mutation testing | High | 🟢 DONE |
| Rector | Automated upgrades and refactors | High | 🟢 DONE |
| Composer Dependency Validation | Dependency conflict prevention | High | 🟢 DONE |
| Compatibility Matrix | Laravel + PHP version testing | Critical | 🟢 DONE |
| Queue Runtime Tests | Queue propagation validation | Critical | 🟢 DONE (sync queue propagation tests shipped `v0.3.1`) |
| Horizon Runtime Tests | Horizon worker lifecycle validation | High | 🟢 DONE (supervisor loop + metrics tests shipped `v0.4.0`; full Redis/Horizon CI job: v0.6.x) |
| Octane Runtime Tests | Long-running worker validation | High | 🔴 PLANNED |
| Benchmark Pipeline | Instrumentation overhead testing | Critical | 🟢 DONE |
| Memory Leak Detection | Long-running process safety | High | 🔴 PLANNED |
| Docker Integration Tests | Redis/MySQL/Horizon integration | High | 🔴 PLANNED |
| OpenTelemetry Compliance Tests | Semantic convention validation | High | 🔴 PLANNED |
| Security Audit Pipeline | Dependency vulnerability scanning | Critical | 🟢 DONE |
| Documentation Validation | Ensure documentation examples work | Medium | 🟡 IN-PROGRESS (docs shipped; automated example validation pending) |
| Code Coverage Reporting | Coverage enforcement and trends | High | 🟢 DONE |
| Package Install Validation | Fresh Laravel installation testing | Critical | 🟢 DONE |
| Parallel Testing Pipeline | Concurrency and runtime testing | High | 🔴 PLANNED |
| Release Automation | Automated changelog/tagging | Medium | 🔴 PLANNED |

---

## Recommended Package Architecture

| Package | Composer |
|---------|----------|
| contracts | `obeserva/contracts` |
| core | `obeserva/core` |
| laravel | `scout/laravel` |
| scout-driver | `obeserva/scout-driver` |
| otel-driver | `obeserva/otel-driver` |
| testing | `obeserva/testing` |

---

## Detailed Development Roadmap

| Version | Module | Sub-Module | Status | Prompt |
|---------|--------|------------|--------|--------|
| v0.1.0 | Foundation | Monorepo Structure | 🟢 DONE | Create a modular monorepo architecture separating contracts, core runtime, Laravel instrumentation, Scout driver, OpenTelemetry driver, and testing utilities. Enforce strict dependency boundaries and package isolation. |
| v0.1.0 | Foundation | Composer Package Split | 🟢 DONE | Create isolated Composer packages for contracts, instrumentation runtime, Laravel integration, Scout adapter, and testing utilities. Ensure semantic versioning compatibility between packages. |
| v0.1.0 | Foundation | CI/CD Infrastructure | 🟢 DONE | Configure enterprise-grade GitHub Actions pipelines for static analysis, mutation testing, benchmarking, compatibility testing, release automation, and runtime safety validation. |
| v0.1.1 | Core Contracts | Span Contracts | 🟢 DONE | Design vendor-neutral span contracts supporting transactions, events, metadata, timing, and distributed tracing concepts without leaking Scout-specific terminology. |
| v0.1.1 | Core Contracts | Trace Context API | 🟢 DONE | Build immutable trace context objects capable of safely propagating distributed trace metadata across HTTP requests, queues, events, notifications, and async workers. |
| v0.1.1 | Core Contracts | Driver Interfaces | 🟢 DONE | Create pluggable interfaces for tracing drivers, exporters, samplers, context storage, and propagation mechanisms following strict dependency inversion principles. |
| v0.2.0 | Core Runtime | Span Lifecycle Engine | 🟢 DONE | Shipped `v0.2.0`: nesting, active span stack, `SpanScope`, completed-span flush buffer, `Tracer::trace()`. Driver export & worker-runtime hardening deferred to v0.5.x–v0.8.x. |
| v0.2.0 | Core Runtime | Context Manager | 🟢 DONE | Shipped `v0.2.0`: `ContextManager` with trace context + active span stack for HTTP requests. Queue worker context restore shipped `v0.3.1`; Horizon/Octane/long-lived CLI isolation: v0.4.0–v0.6.1. |
| v0.2.0 | Core Runtime | Sampling Engine | 🟢 DONE | Shipped `v0.2.0`: `AlwaysOnSampler`, `ProbabilitySampler`, config via `OBESERVA_SAMPLE_RATE`. |
| v0.2.1 | Laravel Integration | Service Provider | 🟢 DONE | Shipped `v0.2.1`: auto-discovery, config publish, deferred middleware, listeners, terminate flush. |
| v0.2.1 | Laravel Integration | HTTP Middleware Instrumentation | 🟢 DONE | Shipped `v0.2.0`–`v0.2.1`: request spans, `RequestSpanEnricher`, route/middleware metadata, response attrs, `obeserva.timing:{segment}` pipeline timing, user context. Auto-instrumentation of every global middleware: not planned (use `obeserva.timing`). |
| v0.2.1 | Laravel Integration | Exception Instrumentation | 🟢 DONE | Shipped `v0.2.1`: `ReportExceptionListener`, HTTP span correlation via `reportable()`, standalone `exception` spans outside requests. Queue/job exception correlation shipped `v0.3.1` (`TraceJobFailedListener`). |
| v0.3.0 | Database Instrumentation | Query Tracing | 🟢 DONE | Shipped `v0.3.0`: `TraceQueryListener`, `QuerySanitizer`, `db.*` child spans, `db.query_count` on request spans. Eloquent model attributes on spans: deferred (v0.4.1+). |
| v0.3.0 | Database Instrumentation | Lazy Loading Detection | 🟢 DONE | Shipped `v0.3.0`: `NPlusOneDetector` flags repeated query patterns on the active span (`db.n_plus_one_detected`). Eloquent `LazyLoadingAttempted` event not available in Laravel 12; pattern-based detection used instead. |
| v0.3.1 | Queue Instrumentation | Queue Trace Propagation | 🟢 DONE | Shipped `v0.3.1`: `Queue::createPayloadUsing` + `TraceContextCarrier` injects W3C context from active HTTP/request spans into job payloads; restored on `JobProcessing`. |
| v0.3.1 | Queue Instrumentation | Failed Job Correlation | 🟢 DONE | Shipped `v0.3.1`: `TraceJobFailedListener` correlates exceptions to active job spans with `queue.result=failed`. |
| v0.3.1 | Queue Instrumentation | Queue Driver Hooks | 🟢 DONE | Shipped `v0.3.1`: `JobProcessing`/`JobProcessed`/`JobFailed` listeners; driver-agnostic via Laravel queue events (sync, Redis, database, SQS). Horizon-specific hooks shipped `v0.4.0`. |
| v0.4.0 | Horizon Integration | Worker Lifecycle Tracing | 🟢 DONE | Shipped `v0.4.0`: `SupervisorLooped`, `WorkerProcessRestarting`, `SupervisorProcessRestarting`, `WorkerStopping` listeners; `horizon.supervisor:*` spans. |
| v0.4.0 | Horizon Integration | Retry Trace Correlation | 🟢 DONE | Shipped `v0.4.0`: `root_trace_id` in carrier, `HorizonRetryCorrelator`, `horizon.retry_of` + `queue.retry_attempt` on job spans. |
| v0.4.0 | Horizon Integration | Throughput Metrics | 🟢 DONE | Shipped `v0.4.0`: `HorizonThroughputMetrics` via `JobReserved`/`JobReleased`; attributes on supervisor spans. |
| v0.4.1 | Cache Instrumentation | Redis Tracing | 🟢 DONE | Shipped `v0.4.1`: `TraceRedisCommandExecutedListener` records `redis.{command}` spans with connection, operation, and `db.duration_ms` from `CommandExecuted` events. |
| v0.4.1 | Cache Instrumentation | Cache Store Hooks | 🟢 DONE | Shipped `v0.4.1`: `TraceCacheEventListener` records `cache.get`/`cache.miss`/`cache.put`/`cache.forget` spans with store, key, hit/miss, and TTL metadata. |
| v0.5.0 | Scout Driver | Scout Span Adapter | 🟢 DONE | Shipped `v0.5.0`: `ScoutSpanExporter` maps Obeserva spans to Scout operations via `ScoutSpanMapper`; lifecycle export on span start/end/flush. |
| v0.5.0 | Scout Driver | Scout Context Bridge | 🟢 DONE | Shipped `v0.5.0`: `ScoutContextBridge` propagates trace/span IDs and attributes as Scout context and request tags. |
| v0.5.0 | Scout Driver | Scout Configuration Layer | 🟢 DONE | Shipped `v0.5.0`: `obeserva.scout.*` config with application name, key, monitoring toggle, and default tags. |
| v0.5.1 | Scout Driver | Advanced Scout Metadata | 🟢 DONE | Shipped `v0.5.1`: `ScoutMetadataEnricher` maps route, queue, and Horizon span attributes to `scout.*` tags; runtime diagnostics and deployment/tenant config. |
| v0.6.0 | OpenTelemetry Alignment | OTel Semantic Conventions | 🟢 DONE | Shipped `v0.6.0`: `OtelSemanticConventionMapper` normalizes HTTP, DB, queue, and cache attributes to OTel semantic convention keys. |
| v0.6.0 | OpenTelemetry Alignment | OTel Export Adapter | 🟢 DONE | Shipped `v0.6.0`: `OtelSpanExporter` batches completed spans as OTel-compatible payloads via `OtelSpanConverter`; `OBESERVA_DRIVER=otel`. |
| v0.6.1 | Runtime Support | Worker Context Isolation | 🟢 DONE | Shipped `v0.6.1`: `WorkerContextResetter`, `WorkerRuntimeDetector`, job-level flush in dedicated queue/Horizon workers. |
| v0.6.1 | Runtime Support | Octane Compatibility | 🟢 DONE | Shipped `v0.6.1`: Octane request/task/tick termination hooks reset tracer and context when `OBESERVA_OCTANE_ISOLATION` is enabled. |
| v0.6.1 | Runtime Support | RoadRunner Compatibility | 🟢 DONE | Shipped `v0.6.1`: optional RoadRunner worker termination hooks when packages are present. |
| v0.6.1 | Runtime Support | Swoole Awareness | 🟢 DONE | Shipped `v0.6.1`: covered via Octane Swoole backend termination hooks. |
| v0.7.0 | Developer Experience | Telescope Integration | 🟢 DONE | Shipped `v0.7.0`: `PublishTraceToTelescope`, `TelescopeTraceEntryFactory`, optional `laravel/telescope` publisher; span snapshots on terminate. |
| v0.7.0 | Developer Experience | Debug Toolbar | 🟢 DONE | Shipped `v0.7.0`: `DebugToolbarMiddleware` injects local HTML trace panel with span tree, timing, and propagation flows when `OBESERVA_DEBUG_TOOLBAR` is enabled. |
| v0.7.1 | Developer Experience | Testing Utilities | 🟡 IN-PROGRESS | Shipped: `FakeTracer`, `assertSpanRecorded()`, nested spans (`v0.2.1`). Added `TraceContextAssert`, `TraceSnapshotBuilder`, `TraceSnapshotAssert`, `InteractsWithObeserva` (`v0.7.1`). |
| v0.7.1 | Developer Experience | Benchmark Suite | 🟢 DONE | Shipped: `benchmark.yml` workflow with span overhead smoke test (`v0.1.0`). Expand with realistic Laravel workloads in future iterations. |
| v0.8.0 | Distributed Systems | Event Propagation | 🔴 PLANNED | Propagate trace context across Laravel events, listeners, notifications, broadcasts, and asynchronous workflows. |
| v0.8.0 | Distributed Systems | Cross-Service Correlation | 🔴 PLANNED | Support distributed correlation IDs and trace continuity for microservices and multi-service Laravel ecosystems. |
| v0.8.1 | Production Engineering | Memory Safety | 🔴 PLANNED | Harden runtime internals against memory leaks in long-running PHP workers, Horizon workers, and Octane environments. |
| v0.8.1 | Production Engineering | Flush Safety | 🔴 PLANNED | Guarantee safe flushing behavior during worker shutdowns, fatal exceptions, deployment restarts, and unexpected runtime termination. |
| v0.9.0 | AI/Advanced Features | Trace Summaries | 🔴 PLANNED | Generate AI-friendly structured trace summaries optimized for debugging workflows and future LLM integrations. |
| v0.9.0 | AI/Advanced Features | Slow Request Causation | 🔴 PLANNED | Build causal relationship graphs between requests, database queries, jobs, events, and cache operations. |
| v1.0.0 | Stable Release | Production Stabilization | 🔴 PLANNED | Finalize stable APIs, documentation, migration guarantees, performance validation, runtime reliability, and enterprise-grade package stability. |
| v1.0.0 | Stable Release | Scout Partnership Readiness | 🔴 PLANNED | Prepare engineering-quality benchmarks, architecture diagrams, technical documentation, runtime comparisons, and feature showcases suitable for presentation to Scout engineering leadership. |

---

## Features Most Likely To Impress Scout APM

| Feature | Why It Matters |
|---------|----------------|
| Queue Trace Propagation | One of the hardest problems in Laravel observability |
| Horizon Worker Instrumentation | Very few vendors handle this deeply |
| Octane/RoadRunner Safety | Demonstrates modern PHP runtime awareness |
| Low Overhead Benchmarks | Critical for production adoption |
| Vendor-Neutral Architecture | Shows senior-level infrastructure thinking |
| OpenTelemetry Alignment | Future-proofs the architecture |
| Memory Leak Protection | Indicates long-running runtime maturity |
| Advanced Testing Utilities | Demonstrates ecosystem/platform engineering thinking |
| Trace Correlation | Shows distributed systems understanding |
| Clean Internal Contracts | Indicates maintainability and extensibility maturity |

---

## Long-Term Goal

The long-term objective is **not** to become:

> “another Scout wrapper.”

The long-term objective **is** to become:

> “the most advanced Laravel instrumentation and observability runtime in the PHP ecosystem.”

If executed properly, this project can evolve into:

- a Laravel observability standard
- an OpenTelemetry-compatible instrumentation layer
- a vendor-neutral runtime abstraction
- a strategic ecosystem project relevant to:
  - Scout APM
  - Sentry
  - Grafana Labs
  - SigNoz
  - Honeycomb
