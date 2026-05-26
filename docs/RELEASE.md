# Release process

This document describes how to cut a monorepo release for Obeserva. Releases are versioned together across all packages under `packages/`.

## Branch workflow

| Branch | Purpose |
|--------|---------|
| `main` | Stable integration; receives merged release branches |
| `release/vX.Y.Z` | Finalize changelog, docs, and version constants before tagging |
| `feature/vX.Y.Z-*` | Versioned feature development |

### Cutting a release branch

```bash
git checkout main
git pull origin main
git checkout -b release/vX.Y.Z
```

On the release branch:

1. Finalize `CHANGELOG.md` (move `[Unreleased]` entries into the version section; set the release date).
2. Confirm every `packages/*/composer.json` has the correct `"version"`.
3. Update README, package READMEs, and `docs/PACKAGES.md` if the public API changed.
4. Add or update `docs/posts/vX.Y.Z-*.md` for the announcement.
5. Run the pre-release gate:

```bash
composer ci
composer pre-push   # optional; requires pcov or Xdebug
```

6. Open a PR from `release/vX.Y.Z` → `main`. Merge when CI is green.

## Tagging

After the release PR merges to `main`:

```bash
git checkout main
git pull origin main
git tag -a vX.Y.Z -m "Release vX.Y.Z"
git push origin vX.Y.Z
```

The monorepo uses a **single repository tag** (`v0.1.0`, `v0.2.0`, …) aligned with the roadmap version. All packages in that release share the same version number in their `composer.json` files.

## GitHub release

1. Open [Releases](https://github.com/zaeem2396/laravel-obeserva/releases) → **Draft a new release**.
2. Choose tag `vX.Y.Z`.
3. Set the release title (see [Release titles](#release-titles) below).
4. Paste the body from `docs/posts/vX.Y.Z-*.md` or from the matching `CHANGELOG.md` section.
5. Publish the release (not pre-release for stable milestones).

## Packagist (when published)

Each package is published independently. After tagging, create matching tags or split publishes per package policy:

| Package | Composer name |
|---------|---------------|
| contracts | `obeserva/contracts` |
| core | `obeserva/core` |
| laravel | `scout/laravel` |
| scout-driver | `obeserva/scout-driver` |
| otel-driver | `obeserva/otel-driver` |
| testing | `obeserva/testing` |

Until packages are on Packagist, consumers install via path repositories or VCS with `@dev` (see [INSTALLATION.md](INSTALLATION.md)).

## Post-release

On `main` after tagging:

1. Add a new empty `## [Unreleased]` section at the top of `CHANGELOG.md`.
2. Update compare links at the bottom of `CHANGELOG.md`.
3. Bump roadmap / internal notes if applicable.
4. Share the announcement post (`docs/posts/`).

## Release titles

| Version | Suggested GitHub release title |
|---------|-------------------------------|
| v0.1.0 | Obeserva v0.1.0 — Foundation |
| v0.1.1 | Obeserva v0.1.1 — Core Contracts |
| v0.2.0 | Obeserva v0.2.0 — Core Runtime |
| v0.2.1 | Obeserva v0.2.1 — Laravel HTTP |
| v0.3.0 | Obeserva v0.3.0 — Database Instrumentation |
| v0.3.1 | Obeserva v0.3.1 — Queue Instrumentation |

## Checklist (copy per release)

- [ ] `release/vX.Y.Z` branch created from `main`
- [ ] `CHANGELOG.md` finalized with date
- [ ] All `packages/*/composer.json` versions match
- [ ] Docs and `docs/posts/` updated
- [ ] `composer ci` passes locally
- [ ] PR merged to `main`
- [ ] Annotated tag `vX.Y.Z` pushed
- [ ] GitHub release published
- [ ] Announcement posted (blog / social / community)
- [ ] `[Unreleased]` section opened on `main`
