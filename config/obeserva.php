<?php

declare(strict_types=1);

return [

    'enabled' => env('OBESERVA_ENABLED', true),

    'driver' => env('OBESERVA_DRIVER', 'noop'),

    'scout' => [
        'enabled' => env('OBESERVA_SCOUT_ENABLED', true),
        'application_name' => env('OBESERVA_SCOUT_APPLICATION_NAME', env('APP_NAME', 'laravel')),
        'key' => env('OBESERVA_SCOUT_KEY', env('SCOUT_KEY', '')),
        'monitoring_enabled' => env('OBESERVA_SCOUT_MONITORING_ENABLED', env('SCOUT_MONITORING_ENABLED', false)),
        'default_tags' => [
            'laravel.env' => env('APP_ENV', 'production'),
        ],
        'deployment_version' => env('OBESERVA_SCOUT_DEPLOYMENT_VERSION', env('APP_VERSION', '')),
        'tenant_id' => env('OBESERVA_SCOUT_TENANT_ID', ''),
        'metadata_enabled' => env('OBESERVA_SCOUT_METADATA_ENABLED', true),
    ],

    'otel' => [
        'enabled' => env('OBESERVA_OTEL_ENABLED', true),
        'service_name' => env('OBESERVA_OTEL_SERVICE_NAME', env('APP_NAME', 'laravel')),
        'service_version' => env('OBESERVA_OTEL_SERVICE_VERSION', env('APP_VERSION', '')),
        'semantic_conventions' => env('OBESERVA_OTEL_SEMANTIC_CONVENTIONS', true),
    ],

    'sampling' => [
        'probability' => (float) env('OBESERVA_SAMPLE_RATE', 1.0),
    ],

    'http' => [
        'middleware_enabled' => env('OBESERVA_HTTP_MIDDLEWARE', true),
        'middleware_timing_alias' => env('OBESERVA_HTTP_MIDDLEWARE_TIMING', true),
    ],

    'exceptions' => [
        'enabled' => env('OBESERVA_EXCEPTION_INSTRUMENTATION', true),
    ],

    'database' => [
        'query_tracing' => env('OBESERVA_DB_QUERY_TRACING', true),
        'lazy_loading_detection' => env('OBESERVA_DB_LAZY_LOADING_DETECTION', true),
    ],

    'queue' => [
        'propagation_enabled' => env('OBESERVA_QUEUE_PROPAGATION', true),
        'job_tracing' => env('OBESERVA_QUEUE_JOB_TRACING', true),
        'failed_job_correlation' => env('OBESERVA_QUEUE_FAILED_CORRELATION', true),
    ],

    'horizon' => [
        'enabled' => env('OBESERVA_HORIZON_ENABLED', true),
        'worker_tracing' => env('OBESERVA_HORIZON_WORKER_TRACING', true),
        'throughput_metrics' => env('OBESERVA_HORIZON_THROUGHPUT_METRICS', true),
        'retry_correlation' => env('OBESERVA_HORIZON_RETRY_CORRELATION', true),
    ],

    'cache' => [
        'enabled' => env('OBESERVA_CACHE_ENABLED', true),
    ],

    'redis' => [
        'command_tracing' => env('OBESERVA_REDIS_COMMAND_TRACING', true),
    ],

    'terminate' => [
        'flush_tracer' => env('OBESERVA_FLUSH_ON_TERMINATE', true),
    ],

    'worker' => [
        'context_isolation' => env('OBESERVA_WORKER_CONTEXT_ISOLATION', true),
        'flush_after_job' => env('OBESERVA_WORKER_FLUSH_AFTER_JOB', true),
        'octane_isolation' => env('OBESERVA_OCTANE_ISOLATION', true),
        'roadrunner_isolation' => env('OBESERVA_ROADRUNNER_ISOLATION', true),
    ],

    'development' => [
        'telescope' => [
            'enabled' => env('OBESERVA_TELESCOPE_ENABLED', false),
        ],
        'debug_toolbar' => [
            'enabled' => env('OBESERVA_DEBUG_TOOLBAR', env('APP_DEBUG', false) && env('APP_ENV') === 'local'),
            'show_propagation' => env('OBESERVA_DEBUG_TOOLBAR_PROPAGATION', true),
        ],
    ],

    'events' => [
        'propagation_enabled' => env('OBESERVA_EVENT_PROPAGATION', true),
        'tracing_enabled' => env('OBESERVA_EVENT_TRACING', true),
    ],

    'notifications' => [
        'tracing_enabled' => env('OBESERVA_NOTIFICATION_TRACING', true),
    ],

    'broadcasts' => [
        'tracing_enabled' => env('OBESERVA_BROADCAST_TRACING', true),
        'propagation_enabled' => env('OBESERVA_BROADCAST_PROPAGATION', true),
    ],

    'correlation' => [
        'enabled' => env('OBESERVA_CORRELATION_ENABLED', true),
        'header' => env('OBESERVA_CORRELATION_HEADER', 'X-Correlation-ID'),
        'propagate_outbound' => env('OBESERVA_CORRELATION_PROPAGATE', true),
    ],

];
