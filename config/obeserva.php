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

];
