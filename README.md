# Obeserva

Laravel-native observability and instrumentation runtime with vendor-neutral architecture, OpenTelemetry-aligned abstractions, and deep Laravel runtime awareness.

## Installation

```bash
composer require scout/laravel
```

## Packages

| Package | Composer name | Description |
|---------|---------------|-------------|
| Contracts | `obeserva/contracts` | Vendor-neutral span, trace context, and driver interfaces |
| Core | `obeserva/core` | Instrumentation runtime (spans, context, sampling) |
| Laravel | `scout/laravel` | Laravel integration (service provider, middleware, hooks) |
| Scout Driver | `obeserva/scout-driver` | Scout APM adapter |
| OTel Driver | `obeserva/otel-driver` | OpenTelemetry exporter adapter |
| Testing | `obeserva/testing` | Fake tracers and test assertions |

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 11, 12, or 13

## Development

```bash
composer install
composer test
composer analyse
composer format:check
```

## License

MIT
