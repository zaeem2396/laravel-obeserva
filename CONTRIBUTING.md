# Contributing to Obeserva

Thank you for contributing to Obeserva.

## Requirements

- PHP 8.3, 8.4, or 8.5
- Composer 2.x
- Optional: `php-pcov` or Xdebug for coverage/mutation testing

## Repository structure

```
src/
  Contracts/
  Core/
  ...Laravel runtime classes
config/
tests/
  Contracts/
  Core/
  Laravel/
  Testing/
```

## Quality checks

```bash
composer ci
composer pre-push
```

## Pull requests

1. Branch from `main`
2. Run `composer ci` before pushing
3. Ensure GitHub Actions pass
4. Update `CHANGELOG.md` under `[Unreleased]` for user-facing changes
