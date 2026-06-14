<?php

declare(strict_types=1);

namespace Obeserva\Testing;

use Obeserva\Contracts\Trace\TraceContextInterface;
use Obeserva\Laravel\Queue\TraceContextCarrier;
use PHPUnit\Framework\AssertionFailedError;

final class TraceContextAssert
{
    public static function assertSameTraceContext(
        TraceContextInterface $expected,
        TraceContextInterface $actual,
    ): void {
        if ($expected->getTraceId() !== $actual->getTraceId()) {
            throw new AssertionFailedError(sprintf(
                'Expected trace id [%s], got [%s].',
                $expected->getTraceId(),
                $actual->getTraceId(),
            ));
        }

        if ($expected->getSpanId() !== $actual->getSpanId()) {
            throw new AssertionFailedError(sprintf(
                'Expected span id [%s], got [%s].',
                $expected->getSpanId(),
                $actual->getSpanId(),
            ));
        }

        if ($expected->getParentSpanId() !== $actual->getParentSpanId()) {
            throw new AssertionFailedError(sprintf(
                'Expected parent span id [%s], got [%s].',
                (string) $expected->getParentSpanId(),
                (string) $actual->getParentSpanId(),
            ));
        }

        if ($expected->isSampled() !== $actual->isSampled()) {
            throw new AssertionFailedError(sprintf(
                'Expected sampled [%s], got [%s].',
                $expected->isSampled() ? 'true' : 'false',
                $actual->isSampled() ? 'true' : 'false',
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    public static function assertTraceparentHeader(
        array $headers,
        string $traceId,
        string $spanId,
        bool $sampled = true,
    ): void {
        $flags = $sampled ? '01' : '00';
        $expected = sprintf('00-%s-%s-%s', $traceId, $spanId, $flags);

        $actual = $headers['traceparent'] ?? $headers['Traceparent'] ?? null;

        if (! is_string($actual)) {
            throw new AssertionFailedError('Expected traceparent header was not present.');
        }

        if ($actual !== $expected) {
            throw new AssertionFailedError(sprintf(
                'Expected traceparent [%s], got [%s].',
                $expected,
                $actual,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function assertQueuePayloadCarriesContext(
        array $payload,
        TraceContextInterface $expected,
    ): void {
        $extracted = TraceContextCarrier::extract($payload);

        if (! $extracted instanceof TraceContextInterface) {
            throw new AssertionFailedError('Expected queue payload to carry Obeserva trace context.');
        }

        self::assertSameTraceContext($expected, $extracted);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function assertQueuePayloadContainsTraceId(array $payload, string $traceId): void
    {
        $extracted = TraceContextCarrier::extract($payload);

        if (! $extracted instanceof TraceContextInterface) {
            throw new AssertionFailedError('Expected queue payload to carry Obeserva trace context.');
        }

        if ($extracted->getTraceId() !== $traceId) {
            throw new AssertionFailedError(sprintf(
                'Expected queue payload trace id [%s], got [%s].',
                $traceId,
                $extracted->getTraceId(),
            ));
        }
    }
}
