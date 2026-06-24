<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Support;

use InvalidArgumentException;

final class ConfigValidator
{
    /** @var list<string> */
    private const array ALLOWED_DRIVERS = ['noop', 'scout', 'otel'];

    /**
     * @return list<string>
     */
    public function validate(): array
    {
        $errors = [];

        $driver = config('obeserva.driver', 'noop');
        $driver = is_string($driver) ? $driver : 'noop';
        if (! in_array($driver, self::ALLOWED_DRIVERS, true)) {
            $errors[] = sprintf(
                'Invalid obeserva.driver "%s". Allowed values: %s.',
                $driver,
                implode(', ', self::ALLOWED_DRIVERS),
            );
        }

        $probability = config('obeserva.sampling.probability', 1.0);
        if (! is_numeric($probability) || (float) $probability < 0.0 || (float) $probability > 1.0) {
            $errors[] = 'obeserva.sampling.probability must be a number between 0.0 and 1.0.';
        }

        if ($driver === 'scout' && ! (bool) config('obeserva.scout.enabled', true)) {
            $errors[] = 'obeserva.driver is "scout" but obeserva.scout.enabled is false.';
        }

        if ($driver === 'otel' && ! (bool) config('obeserva.otel.enabled', true)) {
            $errors[] = 'obeserva.driver is "otel" but obeserva.otel.enabled is false.';
        }

        $topSlowSpans = config('obeserva.summaries.top_slow_spans', 5);
        if (! is_numeric($topSlowSpans) || (int) $topSlowSpans < 0) {
            $errors[] = 'obeserva.summaries.top_slow_spans must be a non-negative integer.';
        }

        return $errors;
    }

    public function assertValid(bool $strict): void
    {
        $errors = $this->validate();

        if ($errors === []) {
            return;
        }

        $message = 'Obeserva configuration is invalid: '.implode(' ', $errors);

        if ($strict) {
            throw new InvalidArgumentException($message);
        }

        logger()->warning($message);
    }
}
