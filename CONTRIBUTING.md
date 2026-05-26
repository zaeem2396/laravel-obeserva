# Contributing to Obeserva

Thank you for contributing to Obeserva. This document covers the monorepo workflow for v0.1.0 and beyond.

## Requirements

- PHP 8.3, 8.4, or 8.5
- Composer 2.x
- Optional: `php-pcov` or Xdebug for coverage and mutation testing locally

## Repository structure

```
packages/
  contracts/     → obeserva/contracts   (no internal obeserva deps)
  core/          → obeserva/core        (depends on contracts)
  laravel/       → scout/laravel        (depends on contracts, core)
  scout-driver/  → obeserva/scout-driver
  otel-driver/   → obeserva/otel-driver
  testing/       → obeserva/testing
```

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) and [docs/PACKAGES.md](docs/PACKAGES.md) for dependency rules.

## Local setup

```bash
composer install
```

## Quality checks

Run the same checks as CI (no coverage driver required):

```bash
composer ci
```

Full gate including coverage and mutation testing (requires pcov or Xdebug):

```bash
composer pre-push
```

Individual commands:

| Command | Purpose |
|---------|---------|
| `composer test` | PHPUnit |
| `composer analyse` | PHPStan max level |
| `composer format:check` | Laravel Pint |
| `composer rector:check` | Rector dry-run |
| `composer validate:deps` | Composer validate |
| `composer test:coverage` | PHPUnit + Clover report |
| `composer infection` | Mutation testing |

## Branching

- `main` — stable integration branch
- `feature/vX.Y.Z-*` — versioned feature work (e.g. `feature/v0.1.0-foundation`)
- Keep commits focused; prefer conventional commit prefixes: `feat`, `fix`, `docs`, `chore`, `ci`, `test`

## Pull requests

1. Branch from `main`
2. Run `composer ci` before pushing
3. Ensure GitHub Actions pass
4. Update `CHANGELOG.md` under `[Unreleased]` for user-facing changes

## Package versioning

Packages in this monorepo follow the roadmap version (currently **v0.1.0**). Path repositories use `@dev` during development; tagged releases will publish to Packagist independently per package.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).
