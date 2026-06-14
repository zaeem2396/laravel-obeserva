<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\DeveloperExperience\PropagationFlowInspector;
use Obeserva\DeveloperExperience\TraceSnapshot;
use PHPUnit\Framework\AssertionFailedError;

final class TraceSnapshotAssert
{
    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertCount(int $expected, array $snapshots): void
    {
        $actual = count($snapshots);

        if ($actual !== $expected) {
            throw new AssertionFailedError(sprintf(
                'Expected %d trace snapshots, got %d.',
                $expected,
                $actual,
            ));
        }
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertNamed(string $name, array $snapshots): TraceSnapshot
    {
        foreach ($snapshots as $snapshot) {
            if ($snapshot->name === $name) {
                return $snapshot;
            }
        }

        throw new AssertionFailedError(sprintf(
            'Expected trace snapshot [%s] was not recorded.',
            $name,
        ));
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertHasAttribute(
        string $name,
        string $key,
        mixed $value,
        array $snapshots,
    ): void {
        $snapshot = self::assertNamed($name, $snapshots);

        if (($snapshot->attributes[$key] ?? null) !== $value) {
            throw new AssertionFailedError(sprintf(
                'Expected snapshot [%s] attribute [%s] to equal [%s], got [%s].',
                $name,
                $key,
                self::stringify($value),
                self::stringify($snapshot->attributes[$key] ?? null),
            ));
        }
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertChildOf(string $childName, string $parentName, array $snapshots): void
    {
        $child = self::assertNamed($childName, $snapshots);
        $parent = self::assertNamed($parentName, $snapshots);

        if ($child->parentSpanId !== $parent->spanId) {
            throw new AssertionFailedError(sprintf(
                'Expected snapshot [%s] to be a child of [%s].',
                $childName,
                $parentName,
            ));
        }
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertSameTraceId(string $traceId, array $snapshots): void
    {
        foreach ($snapshots as $snapshot) {
            if ($snapshot->traceId !== $traceId) {
                throw new AssertionFailedError(sprintf(
                    'Expected all snapshots to share trace id [%s], found [%s] on [%s].',
                    $traceId,
                    $snapshot->traceId,
                    $snapshot->name,
                ));
            }
        }
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertPropagationIncludesQueueSpan(array $snapshots): void
    {
        $summary = (new PropagationFlowInspector)->summarize($snapshots);

        if ($summary->queueSpans === []) {
            throw new AssertionFailedError('Expected propagation summary to include queue spans.');
        }
    }

    /**
     * @param  list<TraceSnapshot>  $snapshots
     */
    public static function assertPropagationIncludesHttpSpan(array $snapshots): void
    {
        $summary = (new PropagationFlowInspector)->summarize($snapshots);

        if ($summary->httpSpans === []) {
            throw new AssertionFailedError('Expected propagation summary to include HTTP spans.');
        }
    }

    private static function stringify(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
