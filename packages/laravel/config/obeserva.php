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

    'queue' => [
        'propagation_enabled' => env('OBESERVA_QUEUE_PROPAGATION', true),
    ],

    'terminate' => [
        'flush_tracer' => env('OBESERVA_FLUSH_ON_TERMINATE', true),
    ],

];
