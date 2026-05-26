<?php

declare(strict_types=1);

return [

    'enabled' => env('OBESERVA_ENABLED', true),

    'driver' => env('OBESERVA_DRIVER', 'noop'),

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

    'terminate' => [
        'flush_tracer' => env('OBESERVA_FLUSH_ON_TERMINATE', true),
    ],

];
