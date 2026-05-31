# Architecture

Obeserva is a single Composer package (`scout/laravel`) with clear internal runtime layers.

## Layer diagram

```
Laravel Application
      |
Obeserva\Laravel (service provider, middleware, listeners)
      |
Obeserva\Core (tracer, spans, context, sampling)
      |
Obeserva\Contracts (interfaces and value objects)
```

## Internal module boundaries

- `Obeserva\Contracts` depends only on PHP.
- `Obeserva\Core` depends on `Obeserva\Contracts`.
- `Obeserva\Laravel` depends on `Obeserva\Core`, `Obeserva\Contracts`, and `illuminate/*`.
- `Obeserva\Testing` provides test doubles and can depend on runtime layers.

## Trace propagation

W3C Trace Context `traceparent` headers are supported via `TraceContext::toPropagationHeaders()` and `fromPropagationHeaders()`.

### Cache instrumentation (v0.4.1)

Laravel cache events drive client spans via `TraceCacheEventListener`:

1. `CacheHit` / `CacheMissed` → `cache.get` / `cache.miss` with `cache.hit` attribute
2. `KeyWritten` → `cache.put` with optional `cache.ttl_seconds`
3. `KeyForgotten` → `cache.forget`

Configuration: `config/obeserva.php` → `cache.*` (see [INSTALLATION.md](INSTALLATION.md)).

### Redis instrumentation (v0.4.1)

`TraceRedisCommandExecutedListener` listens to `CommandExecuted` and records `redis.{command}` spans with connection name and duration.

Configuration: `config/obeserva.php` → `redis.*` (see [INSTALLATION.md](INSTALLATION.md)).

### Scout driver

When `OBESERVA_DRIVER=scout`, completed spans are forwarded to Scout APM in real time:

1. `ScoutSpanMapper` maps Obeserva span kinds to Scout operation names (`HTTP/*`, `External/*`, `Job/*`, etc.)
2. `ScoutContextBridge` applies default tags and propagates trace/span metadata
3. `ScoutSpanExporter` calls the Scout agent on span start/end and `send()` on flush

Requires optional `scoutapp/scout-apm-laravel` and a bound `Scoutapm\ScoutApmAgent`.

## CI/CD

All quality gates run from the package root. See [CI.md](CI.md).
