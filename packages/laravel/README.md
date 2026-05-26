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

**0.3.1** — Queue trace propagation, consumer job spans, failed-job correlation, plus database and HTTP instrumentation from v0.3.0. Horizon hooks ship in v0.4.0.

## Documentation

- [Installation](../../docs/INSTALLATION.md)
- [Architecture](../../docs/ARCHITECTURE.md)

## License

MIT
