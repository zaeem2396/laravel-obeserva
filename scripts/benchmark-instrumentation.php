#!/usr/bin/env php
<?php

declare(strict_types=1);

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Context\ContextManager;
use Obeserva\Core\Memory\CompletedSpanBufferPolicy;
use Obeserva\Core\Sampling\AlwaysOnSampler;
use Obeserva\Core\Tracer;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;

require __DIR__.'/../vendor/autoload.php';

function bench(string $label, callable $callback): float
{
    $start = hrtime(true);
    $callback();
    $elapsedMs = (hrtime(true) - $start) / 1e6;

    echo sprintf("%s: %.2f ms\n", $label, $elapsedMs);

    return $elapsedMs;
}

$context = new ContextManager;
$tracer = new Tracer(new AlwaysOnSampler, $context, $context);
$factory = new SpanSnapshotFactory;

bench('10k flat spans', static function () use ($tracer): void {
    for ($i = 0; $i < 10000; $i++) {
        $span = $tracer->startSpan('bench.flat', SpanKind::Internal);
        $span->end();
    }
});

bench('5k nested span trees (depth 3)', static function () use ($tracer): void {
    for ($i = 0; $i < 5000; $i++) {
        $root = $tracer->startSpan('bench.root', SpanKind::Internal);
        $child = $tracer->startSpan('bench.child', SpanKind::Internal);
        $leaf = $tracer->startSpan('bench.leaf', SpanKind::Internal);
        $leaf->end();
        $child->end();
        $root->end();
    }
});

bench('1k span snapshots', static function () use ($tracer, $factory): void {
    for ($i = 0; $i < 1000; $i++) {
        $span = $tracer->startSpan('bench.snapshot', SpanKind::Internal);
        $span->setAttribute('iteration', $i);
        $span->end();
        $factory->fromSpan($span);
    }
});

$bufferedTracer = new Tracer(
    new AlwaysOnSampler,
    $context,
    $context,
    bufferPolicy: new CompletedSpanBufferPolicy(100),
);

bench('500 spans with buffer auto-flush (limit 100)', static function () use ($bufferedTracer): void {
    for ($i = 0; $i < 500; $i++) {
        $span = $bufferedTracer->startSpan('bench.buffered', SpanKind::Internal);
        $span->end();
    }
});

echo sprintf(
    "Completed spans: %d\n",
    count($tracer->completedSpans()),
);

echo sprintf(
    "Buffered tracer completed spans: %d\n",
    count($bufferedTracer->completedSpans()),
);
