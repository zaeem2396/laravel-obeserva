<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\SpanSnapshotFactory;
use Obeserva\DeveloperExperience\TraceTreeBuilder;
use PHPUnit\Framework\TestCase;

final class TraceTreeBuilderTest extends TestCase
{
    public function test_builds_nested_tree_from_flat_spans(): void
    {
        $factory = new SpanSnapshotFactory;

        $root = new Span('http.request', SpanKind::Server, 'trace', 'root');
        $child = new Span('db.query', SpanKind::Client, 'trace', 'child', 'root');
        $root->end();
        $child->end();

        $forest = (new TraceTreeBuilder)->buildForest([
            $factory->fromSpan($root),
            $factory->fromSpan($child),
        ]);

        $this->assertCount(1, $forest);
        $this->assertSame('http.request', $forest[0]->snapshot->name);
        $this->assertCount(1, $forest[0]->children);
        $this->assertSame('db.query', $forest[0]->children[0]->snapshot->name);
    }
}

final class PropagationFlowInspectorTest extends TestCase
{
    public function test_summarizes_http_and_queue_spans(): void
    {
        $factory = new SpanSnapshotFactory;

        $http = new Span('GET /users', SpanKind::Server, 'trace', 'http');
        $http->setAttribute('http.method', 'GET');
        $http->end();

        $queue = new Span('queue.process', SpanKind::Consumer, 'trace', 'queue', 'http');
        $queue->setAttribute('queue.name', 'default');
        $queue->end();

        $summary = (new PropagationFlowInspector)->summarize([
            $factory->fromSpan($http),
            $factory->fromSpan($queue),
        ]);

        $this->assertSame('trace', $summary->traceId);
        $this->assertSame(2, $summary->spanCount);
        $this->assertSame(['GET /users'], $summary->httpSpans);
        $this->assertSame(['queue.process'], $summary->queueSpans);
        $this->assertTrue($summary->hasParentContext);
    }
}
