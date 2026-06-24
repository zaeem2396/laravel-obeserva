<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\Tests\Analysis;

use Obeserva\DeveloperExperience\Analysis\SpanCategory;
use Obeserva\DeveloperExperience\Analysis\SpanCategoryResolver;
use Obeserva\Testing\TraceSnapshotBuilder;
use PHPUnit\Framework\TestCase;

final class SpanCategoryResolverTest extends TestCase
{
    public function test_categorizes_database_spans(): void
    {
        $snapshot = TraceSnapshotBuilder::make('db.select')
            ->attribute('db.system', 'mysql')
            ->build();

        $this->assertSame(SpanCategory::Database, (new SpanCategoryResolver)->resolve($snapshot));
    }

    public function test_categorizes_event_spans(): void
    {
        $snapshot = TraceSnapshotBuilder::make('event.dispatch:OrderPlaced')->build();

        $this->assertSame(SpanCategory::Event, (new SpanCategoryResolver)->resolve($snapshot));
    }
}
