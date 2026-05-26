# scout/laravel

Laravel-native observability instrumentation for Obeserva.

## Installation

```bash
composer require scout/laravel
```

## Quick start

Configuration is auto-merged. Optional publish:

```bash
php artisan vendor:publish --tag=obeserva-config
```

HTTP requests are traced when `OBESERVA_ENABLED=true` and `OBESERVA_HTTP_MIDDLEWARE=true` (defaults).

## Requirements

- PHP ^8.3
- Laravel ^11.0 \| ^12.0 \| ^13.0

## Version

**0.3.0** — Database query tracing (`db.*` spans), SQL sanitization, N+1 pattern detection, plus full HTTP instrumentation from v0.2.1. Queue propagation ships in v0.3.1.

## Documentation

- [Installation](../../docs/INSTALLATION.md)
- [Architecture](../../docs/ARCHITECTURE.md)

## License

MIT
