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
| `OBESERVA_DRIVER` | `noop` | Driver identifier (`noop` or `scout`) |
| `OBESERVA_SCOUT_ENABLED` | `true` | Enable Scout export when driver is `scout` |
| `OBESERVA_SCOUT_APPLICATION_NAME` | `APP_NAME` | Scout application name |
| `OBESERVA_SCOUT_KEY` | `SCOUT_KEY` | Scout application key |
| `OBESERVA_SCOUT_MONITORING_ENABLED` | `SCOUT_MONITORING_ENABLED` | Scout monitoring toggle |
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

## Releases

Stable versions are tagged on GitHub as `vX.Y.Z` (latest: `v0.5.0`). See [RELEASE.md](RELEASE.md) and the [v0.5.0 announcement](posts/v0.5.0-scout.md).

### Scout driver

Set `OBESERVA_DRIVER=scout` and install the optional Scout agent:

```bash
composer require scoutapp/scout-apm-laravel
```

Obeserva forwards span lifecycle events to Scout via `ScoutSpanExporter`. When the Scout agent is bound in the container (`Scoutapm\ScoutApmAgent`), spans are exported on flush; otherwise export is skipped safely.

Configuration: `config/obeserva.php` → `scout.*` (see environment variables above).

## Local development

```bash
git clone git@github.com:zaeem2396/laravel-obeserva.git
cd laravel-obeserva
composer install
composer ci
```
