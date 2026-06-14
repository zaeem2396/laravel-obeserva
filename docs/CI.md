# Continuous Integration

Obeserva uses GitHub Actions for quality gates. Workflows live in `.github/workflows/`.

## Local parity

```bash
composer ci
composer pre-push
```

## Workflows

- Tests
- PHPStan
- Pint
- Rector
- Composer validation
- Security audit
- Coverage
- Infection
- Benchmark
- Package install validation (fresh Laravel 11/12/13 app + `composer require scout/laravel`)

Laravel 11 matrix jobs set `COMPOSER_NO_SECURITY_BLOCKING=1` because no patched 11.x release exists yet for CVE-2026-48019; Laravel 12/13 jobs update to the latest framework patch before install.
