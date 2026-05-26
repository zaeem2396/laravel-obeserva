# obeserva/testing

Testing utilities for Obeserva package consumers.

## Installation

```bash
composer require --dev obeserva/testing
```

## Usage

```php
use Obeserva\Testing\FakeTracer;

$tracer = new FakeTracer;
$tracer->startSpan('database.query')->end();

$tracer->assertSpanRecorded('database.query');
```

## Version

**0.3.1** — `FakeTracer` with nested span support and assertions (monorepo release; no API changes in this patch).

## License

MIT
