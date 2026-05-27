<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Tests\Horizon;

use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Core\Span\Span;
use Obeserva\Laravel\Horizon\HorizonJobPayloadReader;
use PHPUnit\Framework\TestCase;

final class HorizonJobPayloadReaderTest extends TestCase
{
    public function test_enriches_horizon_job_metadata(): void
    {
        $span = new Span('job', SpanKind::Consumer, 'trace', 'span');

        HorizonJobPayloadReader::enrichSpan($span, [
            'uuid' => 'job-uuid-1',
            'retry_of' => 'original-job',
            'tags' => ['App\\Jobs\\SendEmail', 'mail'],
            'attempts' => 2,
        ]);

        /** @var array<string, mixed> $attributes */
        $attributes = $span->toArray()['attributes'];

        $this->assertSame('job-uuid-1', $attributes['horizon.job_id'] ?? null);
        $this->assertSame('original-job', $attributes['horizon.retry_of'] ?? null);
        $this->assertSame('App\\Jobs\\SendEmail,mail', $attributes['horizon.tags'] ?? null);
        $this->assertSame(2, HorizonJobPayloadReader::retryAttempt(['attempts' => 2]));
        $this->assertTrue(HorizonJobPayloadReader::isRetry(['retry_of' => 'x']));
    }
}
