# Obeserva

[![Tests](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml)
[![PHPStan](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml)
[![Laravel Pint](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml)

Laravel-native observability and instrumentation runtime with vendor-neutral architecture, OpenTelemetry-aligned abstractions, and deep Laravel runtime awareness.

**Current release:** `v0.1.0` (Foundation) — monorepo structure, package split, and CI/CD infrastructure.

## Installation

```bash
composer require scout/laravel
```

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for configuration and path-repository installs.

## Packages

| Package | Composer name | Description |
|---------|---------------|-------------|
| Contracts | [`obeserva/contracts`](packages/contracts) | Vendor-neutral span, trace context, and driver interfaces |
| Core | [`obeserva/core`](packages/core) | Instrumentation runtime (spans, context, sampling) |
| Laravel | [`scout/laravel`](packages/laravel) | Laravel integration (service provider, middleware, config) |
| Scout Driver | [`obeserva/scout-driver`](packages/scout-driver) | Scout APM adapter (scaffold) |
| OTel Driver | [`obeserva/otel-driver`](packages/otel-driver) | OpenTelemetry exporter adapter (scaffold) |
| Testing | [`obeserva/testing`](packages/testing) | Fake tracers and test assertions |

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 11, 12, or 13 (for `scout/laravel`)

## Documentation

| Document | Description |
|----------|-------------|
| [Architecture](docs/ARCHITECTURE.md) | Monorepo layers and dependency rules |
| [Packages](docs/PACKAGES.md) | Per-package reference |
| [Installation](docs/INSTALLATION.md) | Setup and environment variables |
| [CI/CD](docs/CI.md) | GitHub Actions workflows |
| [Contributing](CONTRIBUTING.md) | Development workflow |
| [Changelog](CHANGELOG.md) | Release history |
| [Security](SECURITY.md) | Vulnerability reporting |

## Development

```bash
composer install
composer ci          # PHPUnit, PHPStan, Pint, Rector, validation
composer pre-push    # Full gate (+ coverage & infection, needs pcov/xdebug)
```

## Roadmap

v0.1.0 delivers the **foundation**: modular monorepo, Composer package split, and enterprise-grade CI. Subsequent releases add database instrumentation, queue propagation, Horizon/Octane support, Scout and OpenTelemetry drivers, and production hardening through v1.0.0.

## License

MIT — see [LICENSE](LICENSE).
