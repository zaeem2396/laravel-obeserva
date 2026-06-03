<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Illuminate\Contracts\Foundation\Application;

final readonly class ScoutRuntimeDiagnostics
{
    public function __construct(
        public string $phpVersion,
        public string $laravelVersion,
        public string $appEnv,
        public bool $debug,
    ) {}

    public static function fromApplication(?Application $app): self
    {
        if (!$app instanceof \Illuminate\Contracts\Foundation\Application) {
            return new self(PHP_VERSION, '', 'unknown', false);
        }

        return new self(
            phpVersion: PHP_VERSION,
            laravelVersion: $app->version(),
            appEnv: $app->environment(),
            debug: (bool) $app->make('config')->get('app.debug', false),
        );
    }

    /**
     * @return array<string, string>
     */
    public function toTags(): array
    {
        $tags = [
            'scout.php.version' => $this->phpVersion,
            'scout.app.env' => $this->appEnv,
            'scout.app.debug' => $this->debug ? 'true' : 'false',
        ];

        if ($this->laravelVersion !== '') {
            $tags['scout.laravel.version'] = $this->laravelVersion;
        }

        return $tags;
    }
}
