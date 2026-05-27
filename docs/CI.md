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
- Package install validation
