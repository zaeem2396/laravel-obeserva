# Scout APM integration

Obeserva integrates with Scout APM as an optional export driver (`OBESERVA_DRIVER=scout`).

## Architecture

```
Laravel request/job
    → Obeserva Tracer (span lifecycle)
    → ScoutSpanExporter
    → ScoutSpanMapper + ScoutMetadataEnricher
    → ScoutApmClient (scoutapp/scout-apm-laravel)
    → Scout APM backend
```

## Installation

```bash
composer require scout/laravel scoutapp/scout-apm-laravel
```

```env
OBESERVA_DRIVER=scout
OBESERVA_SCOUT_KEY=your-scout-key
OBESERVA_SCOUT_MONITORING_ENABLED=true
```

## Span mapping

`ScoutSpanMapper` translates Obeserva spans into Scout operations:

- HTTP request spans → Scout web transactions
- Database spans → Scout database operations
- Queue consumer spans → Scout background jobs
- Horizon supervisor spans → Scout metadata on worker operations

## Metadata enrichment

When `OBESERVA_SCOUT_METADATA_ENABLED=true` (default), `ScoutMetadataEnricher` adds:

| Tag | Source |
|-----|--------|
| `scout.route.name` | HTTP route |
| `scout.queue.*` | Queue job attributes |
| `scout.horizon.*` | Horizon supervisor metrics |
| `scout.deployment.version` | `OBESERVA_SCOUT_DEPLOYMENT_VERSION` |
| `scout.tenant.id` | `OBESERVA_SCOUT_TENANT_ID` |
| `scout.obeserva.version` | Obeserva package version (v1.0.0+) |
| `scout.php.version` | PHP runtime |
| `scout.laravel.version` | Laravel version |

## Differentiation vs Scout-only

| Capability | Scout agent alone | Obeserva + Scout |
|------------|-------------------|------------------|
| Queue W3C propagation | Limited | Full payload injection |
| Horizon lifecycle | Basic | Supervisor/retry/throughput spans |
| Octane isolation | N/A | Context reset per request |
| Trace summaries | N/A | JSON summaries + causation |
| OTel export | N/A | Switch driver to `otel` without code changes |
| Testing utilities | N/A | `FakeTracer`, assertions |

## Diagnostics

```bash
php artisan obeserva:status
```

Verify driver, enabled features, and package version before enabling Scout in production.

## Further reading

- [Installation](INSTALLATION.md)
- [Benchmarks](BENCHMARKS.md)
- [v0.5.0 Scout driver announcement](posts/v0.5.0-scout.md)
- [v0.5.1 Advanced metadata](posts/v0.5.1-scout-metadata.md)
