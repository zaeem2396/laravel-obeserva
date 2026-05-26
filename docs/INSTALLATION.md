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
| `OBESERVA_DRIVER` | `noop` | Driver identifier (Scout/OTel drivers in future releases) |
| `OBESERVA_SAMPLE_RATE` | `1.0` | Sampling probability (0.0–1.0) |
| `OBESERVA_HTTP_MIDDLEWARE` | `true` | Register HTTP trace middleware |
| `OBESERVA_QUEUE_PROPAGATION` | `true` | Queue propagation (v0.3.1+) |

## Monorepo development

```bash
git clone git@github.com:zaeem2396/laravel-obeserva.git
cd laravel-obeserva
composer install
composer ci
```

## Requirements

- **PHP** 8.3, 8.4, or 8.5
- **Laravel** 11, 12, or 13 (for `scout/laravel`)

## Releases

Stable versions are tagged on GitHub as `vX.Y.Z` (latest: `v0.2.0`). See [RELEASE.md](RELEASE.md) and the [v0.2.0 announcement](posts/v0.2.0-core-runtime.md).

### Manual spans in application code

```php
use Obeserva\Laravel\Facades\Obeserva;
use Obeserva\Contracts\Span\SpanKind;

$span = Obeserva::startSpan('process-order', SpanKind::Internal);
$span->setAttribute('order.id', $orderId);

// ... work ...

$span->end();
```

Or use a scope for automatic completion:

```php
$scope = Obeserva::trace('process-order');
$scope->span->setAttribute('order.id', $orderId);
// span ends when $scope goes out of scope
```

## Path repository install (testing unreleased changes)

```bash
composer config repositories.obeserva path "/absolute/path/to/laravel-obeserva/packages/*"
composer config minimum-stability dev
composer config prefer-stable true
composer require scout/laravel:@dev
```
