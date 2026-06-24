# Upgrade guide

This guide covers upgrading Obeserva (`scout/laravel`) from pre-1.0 releases to **v1.0.0** and semver expectations for the 1.x line.

## Before you upgrade

1. Review the [CHANGELOG](../CHANGELOG.md) for your current version through `1.0.0`.
2. Run your test suite with `OBESERVA_DRIVER=noop` first, then your production driver.
3. Publish the latest config if you maintain a published copy:

```bash
php artisan vendor:publish --tag=obeserva-config --force
```

## v0.9.x → v1.0.0

**v1.0.0 is backward compatible** with v0.9.x configuration and public APIs. No breaking changes to `Obeserva\Contracts` interfaces.

### New features

| Feature | Env / config | Description |
|---------|--------------|-------------|
| Package version | `PackageVersion::VERSION` | Stable `1.0.0` constant for diagnostics |
| Status command | `OBESERVA_STATUS_COMMAND` (default `true`) | `php artisan obeserva:status` |
| Config validation | `OBESERVA_CONFIG_STRICT` (default `false`) | Fail boot on invalid config when `true` |
| Scout version tag | automatic when metadata enabled | `scout.obeserva.version` on Scout exports |

### Recommended post-upgrade checks

```bash
php artisan obeserva:status
php artisan obeserva:status --json
```

Enable strict validation in staging before production:

```env
OBESERVA_CONFIG_STRICT=true
```

## Version history summary

| Version | Theme | Notable changes |
|---------|-------|-----------------|
| v0.1.x | Foundation | Monorepo, CI, contracts |
| v0.2.x | Core runtime | Tracer, sampling, HTTP middleware |
| v0.3.x | Database & queue | Query tracing, W3C queue propagation |
| v0.4.x | Horizon & cache | Supervisor spans, Redis/cache instrumentation |
| v0.5.x | Scout driver | Scout APM export, advanced metadata |
| v0.6.x | OpenTelemetry & workers | OTel driver, Octane/RoadRunner isolation |
| v0.7.x | Developer experience | Telescope, debug toolbar, testing utilities |
| v0.8.x | Distributed & production | Event propagation, correlation, memory/flush safety |
| v0.9.x | AI/advanced | Trace summaries, causation graphs |
| **v1.0.0** | **Stable release** | API stability guarantees, diagnostics, docs |

## Semver from 1.0.0

See [API_STABILITY.md](API_STABILITY.md) for the stability policy on `Obeserva\Contracts` and configuration keys.

## Getting help

- [Installation](INSTALLATION.md)
- [Scout integration](SCOUT_INTEGRATION.md)
- [GitHub Issues](https://github.com/zaeem2396/laravel-obeserva/issues)
