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
