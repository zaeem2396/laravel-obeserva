# Obeserva

[![Tests](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml)
[![PHPStan](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml)
[![Laravel Pint](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml)

Laravel-native observability and instrumentation runtime with OpenTelemetry-aligned abstractions and deep Laravel runtime awareness.

**Current release:** [`v0.6.1`](docs/posts/v0.6.1-worker-context.md) (Worker Context Isolation). **Next:** v0.7.0 (Developer Experience).

## Table of contents

- [Installation](#installation)
- [What you get](#what-you-get)
- [Configuration](#configuration)
- [Instrumentation areas](#instrumentation-areas)
- [Modules](#modules)
- [Documentation index](#documentation-index)
- [Development](#development)
- [License](#license)

## Installation

```bash
composer require scout/laravel
```

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for configuration and environment variables.

## What you get

- **HTTP**: request spans, route metadata, middleware timing, exception correlation
- **Database**: `db.*` child spans, SQL sanitization, N+1 detection
- **Queue**: W3C trace propagation in payloads, consumer spans, failed-job correlation
- **Horizon**: supervisor/worker lifecycle spans, throughput metrics, retry correlation
- **Cache**: cache hit/miss/write/forget spans
- **Redis**: per-command spans via `CommandExecuted` events
- **Scout** (optional): export spans to Scout APM when `OBESERVA_DRIVER=scout`; advanced `scout.*` metadata when enabled
- **OpenTelemetry** (optional): export OTel-compatible span payloads when `OBESERVA_DRIVER=otel`
- **Worker context isolation**: safe tracer reset between jobs in queue, Horizon, Octane, and RoadRunner workers
- **Developer experience** *(v0.7.0, pending release)*: Telescope trace inspection and local debug toolbar

## Configuration

- **Config file**: publish `config/obeserva.php` via `php artisan vendor:publish --tag=obeserva-config`
- **Environment variables**: documented in [docs/INSTALLATION.md](docs/INSTALLATION.md)

## Instrumentation areas

- **HTTP**: enabled by `OBESERVA_HTTP_MIDDLEWARE`
- **Database**: enabled by `OBESERVA_DB_QUERY_TRACING`
- **Queue**: enabled by `OBESERVA_QUEUE_JOB_TRACING` and propagation by `OBESERVA_QUEUE_PROPAGATION`
- **Horizon**: enabled by `OBESERVA_HORIZON_ENABLED` (requires `laravel/horizon`)
- **Cache**: enabled by `OBESERVA_CACHE_ENABLED`
- **Redis**: enabled by `OBESERVA_REDIS_COMMAND_TRACING`
- **Scout**: enabled by `OBESERVA_DRIVER=scout` (requires `scoutapp/scout-apm-laravel`); metadata via `OBESERVA_SCOUT_METADATA_ENABLED`
- **OpenTelemetry**: enabled by `OBESERVA_DRIVER=otel`; semantic conventions via `OBESERVA_OTEL_SEMANTIC_CONVENTIONS`
- **Worker isolation**: enabled by `OBESERVA_WORKER_CONTEXT_ISOLATION`; Octane via `OBESERVA_OCTANE_ISOLATION`
- **Telescope** *(v0.7.0)*: enabled by `OBESERVA_TELESCOPE_ENABLED` (requires `laravel/telescope`)
- **Debug toolbar** *(v0.7.0)*: enabled by `OBESERVA_DEBUG_TOOLBAR` (defaults to local + `APP_DEBUG`)

## Modules

| Module | Namespace | Description |
|--------|-----------|-------------|
| Contracts | `Obeserva\Contracts` | Vendor-neutral span, trace context, and driver interfaces |
| Core | `Obeserva\Core` | Instrumentation runtime (spans, context, sampling) |
| Laravel | `Obeserva\Laravel` | Service provider, middleware, listeners, and config integration |
| Scout Driver | `Obeserva\ScoutDriver` | Scout APM span adapter and context bridge |
| Otel Driver | `Obeserva\OtelDriver` | OpenTelemetry semantic conventions and export adapter |
| Runtime | `Obeserva\Laravel\Runtime` | Worker runtime detection and context isolation |
| Developer Experience | `Obeserva\DeveloperExperience` | Trace snapshots, Telescope publisher, and debug toolbar *(v0.7.0)* |
| Testing | `Obeserva\Testing` | `FakeTracer` for test assertions |

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 11, 12, or 13

## Documentation index

| Document | Description |
|----------|-------------|
| [Architecture](docs/ARCHITECTURE.md) | Runtime layering and boundaries |
| [Modules](docs/PACKAGES.md) | Module-level reference |
| [Installation](docs/INSTALLATION.md) | Setup and environment variables |
| [CI/CD](docs/CI.md) | GitHub Actions workflows |
| [Contributing](CONTRIBUTING.md) | Development workflow |
| [Changelog](CHANGELOG.md) | Release history |
| [Release process](docs/RELEASE.md) | Tagging and release checklist |
| [Roadmap](ROADMAP.md) | Development status and milestones |
| [Security](SECURITY.md) | Vulnerability reporting |

## Development

```bash
composer install
composer ci
composer pre-push
```

## License

MIT — see [LICENSE](LICENSE).
