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

**0.1.0** — `FakeTracer` with span recording and assertions.

## License

MIT
