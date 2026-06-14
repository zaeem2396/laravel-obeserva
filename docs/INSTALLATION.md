# Installation

## Laravel application

```bash
composer require scout/laravel
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=obeserva-config
```

### Environment variables

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_ENABLED` | `true` | Master switch |
| `OBESERVA_DRIVER` | `noop` | Driver identifier (`noop`, `scout`, or `otel`) |
| `OBESERVA_SCOUT_ENABLED` | `true` | Enable Scout export when driver is `scout` |
| `OBESERVA_SCOUT_APPLICATION_NAME` | `APP_NAME` | Scout application name |
| `OBESERVA_SCOUT_KEY` | `SCOUT_KEY` | Scout application key |
| `OBESERVA_SCOUT_MONITORING_ENABLED` | `SCOUT_MONITORING_ENABLED` | Scout monitoring toggle |
| `OBESERVA_SCOUT_DEPLOYMENT_VERSION` | `APP_VERSION` | Deployment/build version tag |
| `OBESERVA_SCOUT_TENANT_ID` | _(empty)_ | Multi-tenant identifier tag |
| `OBESERVA_SCOUT_METADATA_ENABLED` | `true` | Laravel-aware `scout.*` metadata enrichment |
| `OBESERVA_OTEL_ENABLED` | `true` | Enable OTel export when driver is `otel` |
| `OBESERVA_OTEL_SERVICE_NAME` | `APP_NAME` | OTel `service.name` resource attribute |
| `OBESERVA_OTEL_SERVICE_VERSION` | `APP_VERSION` | OTel `service.version` resource attribute |
| `OBESERVA_OTEL_SEMANTIC_CONVENTIONS` | `true` | Normalize span attributes to OTel semantic conventions |
| `OBESERVA_SAMPLE_RATE` | `1.0` | Sampling probability (0.0–1.0) |
| `OBESERVA_HTTP_MIDDLEWARE` | `true` | Register HTTP trace middleware |
| `OBESERVA_HTTP_MIDDLEWARE_TIMING` | `true` | Register `obeserva.timing` middleware alias |
| `OBESERVA_EXCEPTION_INSTRUMENTATION` | `true` | Hook Laravel reportable exceptions |
| `OBESERVA_FLUSH_ON_TERMINATE` | `true` | Flush tracer completed spans on terminate |
| `OBESERVA_DB_QUERY_TRACING` | `true` | Record `db.*` spans for SQL queries |
| `OBESERVA_DB_LAZY_LOADING_DETECTION` | `true` | Detect repeated query patterns (N+1) |
| `OBESERVA_QUEUE_PROPAGATION` | `true` | Inject trace context into queue payloads |
| `OBESERVA_QUEUE_JOB_TRACING` | `true` | Consumer spans for job processing |
| `OBESERVA_QUEUE_FAILED_CORRELATION` | `true` | Correlate failed jobs to traces |
| `OBESERVA_HORIZON_ENABLED` | `true` | Horizon instrumentation (requires `laravel/horizon`) |
| `OBESERVA_HORIZON_WORKER_TRACING` | `true` | Supervisor/worker lifecycle spans |
| `OBESERVA_HORIZON_THROUGHPUT_METRICS` | `true` | Job reserved/released counters on spans |
| `OBESERVA_HORIZON_RETRY_CORRELATION` | `true` | Root trace + retry attempt attributes |
| `OBESERVA_CACHE_ENABLED` | `true` | Cache instrumentation (Cache hit/miss/write/forget) |
| `OBESERVA_REDIS_COMMAND_TRACING` | `true` | Redis command spans via `CommandExecuted` events |
| `OBESERVA_TELESCOPE_ENABLED` | `false` | Publish span snapshots to Laravel Telescope on terminate |
| `OBESERVA_DEBUG_TOOLBAR` | `APP_DEBUG && APP_ENV=local` | Inject local HTML trace toolbar into HTML responses |
| `OBESERVA_DEBUG_TOOLBAR_PROPAGATION` | `true` | Show queue/HTTP propagation summary in the debug toolbar |

## Releases

Stable versions are tagged on GitHub as `vX.Y.Z` (latest: `v0.7.0`). See [RELEASE.md](RELEASE.md) and the [v0.7.0 announcement](posts/v0.7.0-developer-experience.md).

### Scout driver

Set `OBESERVA_DRIVER=scout` and install the optional Scout agent:

```bash
composer require scoutapp/scout-apm-laravel
```

Obeserva forwards span lifecycle events to Scout via `ScoutSpanExporter`. When the Scout agent is bound in the container (`Scoutapm\ScoutApmAgent`), spans are exported on flush; otherwise export is skipped safely.

Configuration: `config/obeserva.php` → `scout.*` (see environment variables above).

When `OBESERVA_SCOUT_METADATA_ENABLED=true` (default), Obeserva enriches Scout with `scout.route.name`, `scout.queue.*`, `scout.horizon.*`, deployment version, tenant ID, and PHP/Laravel runtime diagnostics.

### OpenTelemetry driver

Set `OBESERVA_DRIVER=otel` for OTel-compatible span export. Spans are converted on end and batched on flush — no changes to Laravel instrumentation are required.

Optional: install `open-telemetry/opentelemetry` for OTLP export to a collector.

Configuration: `config/obeserva.php` → `otel.*` (see environment variables above).

### Developer experience

Enable local trace inspection without changing your production driver:

```bash
# Optional Telescope integration
composer require laravel/telescope --dev
```

| Variable | Default | Description |
|----------|---------|-------------|
| `OBESERVA_TELESCOPE_ENABLED` | `false` | Publish span snapshots to Telescope on terminate |
| `OBESERVA_DEBUG_TOOLBAR` | local + `APP_DEBUG` | Inject HTML trace toolbar into responses |
| `OBESERVA_DEBUG_TOOLBAR_PROPAGATION` | `true` | Show queue/HTTP propagation summary in toolbar |

When either feature is enabled, `SpanSnapshotCollector` records completed spans via `CompositeSpanLifecycleExporter` alongside the configured driver exporter.

Configuration: `config/obeserva.php` → `development.*`.

## Local development

```bash
git clone git@github.com:zaeem2396/laravel-obeserva.git
cd laravel-obeserva
composer install
composer ci
```
