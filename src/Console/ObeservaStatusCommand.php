<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Console;

use Illuminate\Console\Command;
use Obeserva\Laravel\Support\RuntimeDiagnosticsBuilder;

final class ObeservaStatusCommand extends Command
{
    protected $signature = 'obeserva:status {--json : Output diagnostics as JSON}';

    protected $description = 'Display Obeserva runtime diagnostics and configuration summary';

    public function handle(RuntimeDiagnosticsBuilder $builder): int
    {
        $diagnostics = $builder->build();

        if ((bool) $this->option('json')) {
            $this->line(json_encode($diagnostics->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        }

        $this->components->twoColumnDetail('Package version', $diagnostics->packageVersion);
        $this->components->twoColumnDetail('Driver', $diagnostics->driver);
        $this->components->twoColumnDetail('Enabled', $diagnostics->enabled ? 'yes' : 'no');
        $this->components->twoColumnDetail('Worker runtime', $diagnostics->workerRuntime);
        $this->components->twoColumnDetail('Sample rate', (string) $diagnostics->sampleRate);
        $this->components->twoColumnDetail('PHP', $diagnostics->phpVersion);
        $this->components->twoColumnDetail('Laravel', $diagnostics->laravelVersion !== '' ? $diagnostics->laravelVersion : 'n/a');
        $this->components->twoColumnDetail('Environment', $diagnostics->appEnv);

        $this->newLine();
        $this->components->info('Enabled features');

        foreach ($diagnostics->features as $feature => $enabled) {
            $this->components->twoColumnDetail($feature, $enabled ? 'on' : 'off');
        }

        return self::SUCCESS;
    }
}
