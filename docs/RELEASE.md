# Release process

This document describes how to cut a release for Obeserva (`scout/laravel`).

## Branch workflow

| Branch | Purpose |
|--------|---------|
| `main` | Stable integration branch |
| `release/vX.Y.Z` | Finalize changelog and docs before tagging |
| `feature/*` | Feature development |

## Pre-release checklist

1. Finalize `CHANGELOG.md` and set release date.
2. Confirm root `composer.json` version if needed.
3. Update README/docs and announcement post in `docs/posts/`.
4. Run: 

```bash
composer ci
composer pre-push
```

5. Merge release PR to `main`.

## Tagging

```bash
git checkout main
git pull origin main
git tag -a vX.Y.Z -m "Release vX.Y.Z"
git push origin vX.Y.Z
```

## GitHub release

1. Open [Releases](https://github.com/zaeem2396/laravel-obeserva/releases) → **Draft a new release**.
2. Choose tag `vX.Y.Z`.
3. Paste the body from `docs/posts/vX.Y.Z-*.md` or the matching `CHANGELOG.md` section.
4. Publish the release (not pre-release for stable milestones).

## Release titles

| Version | Suggested GitHub release title |
|---------|-------------------------------|
| v0.8.0 | Obeserva v0.8.0 — Distributed Systems (Event Propagation & Correlation) |
| v0.7.1 | Obeserva v0.7.1 — Testing Utilities |
| v0.7.0 | Obeserva v0.7.0 — Developer Experience (Telescope & Debug Toolbar) |
| v0.6.1 | Obeserva v0.6.1 — Worker Context Isolation |
| v0.6.0 | Obeserva v0.6.0 — OpenTelemetry Alignment |
| v0.5.1 | Obeserva v0.5.1 — Advanced Scout Metadata |
| v0.5.0 | Obeserva v0.5.0 — Scout APM Driver |
| v0.4.1 | Obeserva v0.4.1 — Cache & Redis Instrumentation |
| v0.4.0 | Obeserva v0.4.0 — Horizon Integration |
| v0.3.1 | Obeserva v0.3.1 — Queue Instrumentation |
| v0.3.0 | Obeserva v0.3.0 — Database Instrumentation |
| v0.2.1 | Obeserva v0.2.1 — Laravel HTTP |
| v0.2.0 | Obeserva v0.2.0 — Core Runtime |
| v0.1.0 | Obeserva v0.1.0 — Foundation |

## Published releases

| Tag | Announcement |
|-----|--------------|
| `v0.8.0` | [docs/posts/v0.8.0-distributed-systems.md](posts/v0.8.0-distributed-systems.md) |
| `v0.7.1` | [docs/posts/v0.7.1-testing-utilities.md](posts/v0.7.1-testing-utilities.md) |
| `v0.7.0` | [docs/posts/v0.7.0-developer-experience.md](posts/v0.7.0-developer-experience.md) |
| `v0.6.1` | [docs/posts/v0.6.1-worker-context.md](posts/v0.6.1-worker-context.md) |
| `v0.6.0` | [docs/posts/v0.6.0-otel.md](posts/v0.6.0-otel.md) |
| `v0.5.1` | [docs/posts/v0.5.1-scout-metadata.md](posts/v0.5.1-scout-metadata.md) |
| `v0.5.0` | [docs/posts/v0.5.0-scout.md](posts/v0.5.0-scout.md) |
| `v0.4.1` | [docs/posts/v0.4.1-cache.md](posts/v0.4.1-cache.md) |
| `v0.4.0` | [docs/posts/v0.4.0-horizon.md](posts/v0.4.0-horizon.md) |
| `v0.3.1` | [docs/posts/v0.3.1-queue.md](posts/v0.3.1-queue.md) |
| `v0.3.0` | [docs/posts/v0.3.0-database.md](posts/v0.3.0-database.md) |
| `v0.2.1` | [docs/posts/v0.2.1-laravel-http.md](posts/v0.2.1-laravel-http.md) |
| `v0.2.0` | [docs/posts/v0.2.0-core-runtime.md](posts/v0.2.0-core-runtime.md) |
| `v0.1.0` | [docs/posts/v0.1.0-foundation.md](posts/v0.1.0-foundation.md) |

## Checklist (copy per release)

- [ ] `release/vX.Y.Z` branch created from `main`
- [ ] `CHANGELOG.md` finalized with date
- [ ] Docs and `docs/posts/` updated
- [ ] `composer ci` passes locally
- [ ] PR merged to `main`
- [ ] Annotated tag `vX.Y.Z` pushed
- [ ] GitHub release published
- [ ] `[Unreleased]` section opened on `main`
