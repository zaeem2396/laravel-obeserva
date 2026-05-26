# Continuous Integration

Obeserva uses GitHub Actions for all quality gates. Workflows live in `.github/workflows/`.

## Workflows

| Workflow | File | Trigger | Purpose |
|----------|------|---------|---------|
| PHPStan | `phpstan.yml` | push, PR | Static analysis (PHP 8.3–8.5) |
| Laravel Pint | `pint.yml` | push, PR | Code style |
| Tests | `tests.yml` | push, PR | PHPUnit + Laravel compatibility matrix |
| Composer Validate | `composer-validate.yml` | push, PR | `composer.json` validation |
| Security Audit | `security.yml` | push, PR, daily | `composer audit` |
| Code Coverage | `coverage.yml` | push to main, PR | Clover report artifact |
| Infection | `infection.yml` | push to main, PR, weekly | Mutation testing |
| Rector | `rector.yml` | push, PR | Refactor dry-run |
| Benchmark | `benchmark.yml` | push to main, PR | Span overhead smoke test |
| Package Install | `package-install.yml` | push, PR | Fresh Laravel + `scout/laravel` |

## Compatibility matrix

| Laravel | PHP | Testbench |
|---------|-----|-----------|
| 11.x | 8.3, 8.4 | ^9.0 |
| 12.x | 8.3, 8.4 | ^10.0 |
| 13.x | 8.4, 8.5 | ^11.0 |

## Local parity

```bash
# Matches most CI jobs (no coverage extension required)
composer ci

# Full gate (requires pcov or Xdebug)
composer pre-push
```

## Planned pipelines (post v0.1.0)

- Queue runtime tests
- Horizon worker tests
- Octane / RoadRunner tests
- Docker integration (Redis, MySQL, Horizon)
- OpenTelemetry semantic convention compliance
- Documentation example validation
- Parallel testing
- Release automation
