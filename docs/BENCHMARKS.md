# Benchmarks

Obeserva ships an instrumentation benchmark suite to validate low-overhead tracing in production workloads.

## Running locally

```bash
php scripts/benchmark-instrumentation.php
```

## CI pipeline

The [benchmark workflow](../.github/workflows/benchmark.yml) runs on every push and pull request. It measures:

| Scenario | What it measures |
|----------|------------------|
| Flat spans | `startSpan` / `end` without nesting |
| Nested spans | 10-level deep span stacks |
| Snapshot collection | Span snapshot factory overhead (dev tooling path) |
| Buffered flush | Completed-span buffer and export flush |

## Design goals

- **Sub-millisecond overhead** per span in noop driver mode for typical HTTP requests
- **Bounded memory** via `OBESERVA_MAX_COMPLETED_SPANS` and pressure flush (v0.8.1+)
- **No application exceptions** from export failures (`TracerFlushGuard`, v0.8.1+)

## Interpreting results

Benchmark output reports operations per second and microseconds per operation. Compare against your baseline with `OBESERVA_ENABLED=false` only in local profiling — production should keep instrumentation enabled with appropriate sampling (`OBESERVA_SAMPLE_RATE`).

## Scout partnership context

For Scout engineering review, benchmarks demonstrate that Obeserva adds instrumentation **before** Scout export — the noop driver isolates runtime cost from network export. When `OBESERVA_DRIVER=scout`, additional overhead depends on the Scout agent and network conditions.

See [SCOUT_INTEGRATION.md](SCOUT_INTEGRATION.md) for the full export pipeline.
