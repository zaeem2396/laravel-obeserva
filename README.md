# Obeserva

[![Tests](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/tests.yml)
[![PHPStan](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/phpstan.yml)
[![Laravel Pint](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml/badge.svg)](https://github.com/zaeem2396/laravel-obeserva/actions/workflows/pint.yml)

Laravel-native observability and instrumentation runtime with OpenTelemetry-aligned abstractions and deep Laravel runtime awareness.

**Current release:** [`v0.4.0`](docs/posts/v0.4.0-horizon.md) (Horizon).

## Installation

```bash
composer require scout/laravel
```

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for configuration.

## Modules

| Module | Namespace | Description |
|--------|-----------|-------------|
| Contracts | `Obeserva\Contracts` | Vendor-neutral span, trace context, and driver interfaces |
| Core | `Obeserva\Core` | Instrumentation runtime (spans, context, sampling) |
| Laravel | `Obeserva\Laravel` | Service provider, middleware, listeners, and config integration |
| Testing | `Obeserva\Testing` | `FakeTracer` for test assertions |

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 11, 12, or 13

## Documentation

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
