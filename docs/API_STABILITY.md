# API stability policy

Obeserva **v1.0.0** establishes semver guarantees for production adopters.

## Stable surface (`Obeserva\Contracts`)

The following namespaces are **stable** for the 1.x major line:

- `Obeserva\Contracts\Driver\*` — tracer, exporter, sampler, propagation interfaces
- `Obeserva\Contracts\Span\*` — span kinds and span interface
- `Obeserva\Contracts\Trace\*` — trace context interface

### What we guarantee

| Change type | 1.x policy |
|-------------|------------|
| Remove or rename a public contract method | **Breaking** — requires 2.0.0 |
| Add optional method to interface | Avoided; prefer new interface |
| Add new contract interface | Minor release |
| Change method signature | **Breaking** — requires 2.0.0 |
| Behavioral fix matching documented contract | Patch release |

## Semi-stable surface

These areas may receive additive changes in minor releases:

- `config/obeserva.php` keys — new keys are additive; renaming requires deprecation cycle
- `OBESERVA_*` environment variables — same as config keys
- `Obeserva\Testing\*` — testing helpers may expand in minors

## Internal surface

The following are **not** covered by semver guarantees:

- `Obeserva\Laravel\Listeners\*`
- `Obeserva\DeveloperExperience\*` internals (public DTOs like `TraceSummary` are stable for read-only use)
- Driver implementation details in `Obeserva\ScoutDriver\*` and `Obeserva\OtelDriver\*`

Use `@internal` annotations and package-private patterns where applicable.

## Deprecation process

1. Mark API deprecated in PHPDoc and CHANGELOG.
2. Emit `E_USER_DEPRECATED` or log warnings for one minor release minimum.
3. Remove in the next major release.

## Version constant

```php
use Obeserva\Laravel\Support\PackageVersion;

PackageVersion::VERSION; // '1.0.0'
```

This constant is updated on every tagged release.
