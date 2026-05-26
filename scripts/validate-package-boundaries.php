<?php

declare(strict_types=1);

/**
 * Validates monorepo package dependency boundaries for v0.1.0.
 *
 * @see docs/ARCHITECTURE.md
 */
$root = dirname(__DIR__);

/** @var array<string, list<string>> */
$allowed = [
    'contracts' => [],
    'core' => ['obeserva/contracts'],
    'laravel' => ['obeserva/contracts', 'obeserva/core'],
    'scout-driver' => ['obeserva/contracts', 'obeserva/core'],
    'otel-driver' => ['obeserva/contracts', 'obeserva/core'],
    'testing' => ['obeserva/contracts', 'obeserva/core'],
];

$forbiddenObeserva = [
    'contracts' => ['obeserva/core', 'obeserva/scout-driver', 'obeserva/otel-driver', 'obeserva/testing', 'scout/laravel'],
];

$errors = [];

foreach ($allowed as $package => $permitted) {
    $composerPath = "{$root}/packages/{$package}/composer.json";

    if (! is_readable($composerPath)) {
        $errors[] = "Missing composer.json for package [{$package}].";

        continue;
    }

    /** @var array{require?: array<string, string>} $composer */
    $composer = json_decode((string) file_get_contents($composerPath), true, 512, JSON_THROW_ON_ERROR);
    $requires = array_keys($composer['require'] ?? []);
    $obeservaRequires = array_values(array_filter($requires, static fn (string $name): bool => str_starts_with($name, 'obeserva/') || $name === 'scout/laravel'));

    foreach ($permitted as $dep) {
        if (! in_array($dep, $obeservaRequires, true)) {
            $errors[] = "Package [{$package}] must require [{$dep}].";
        }
    }

    foreach ($obeservaRequires as $dep) {
        if (! in_array($dep, $permitted, true)) {
            $errors[] = "Package [{$package}] must not require [{$dep}].";
        }
    }

    foreach ($forbiddenObeserva[$package] ?? [] as $dep) {
        if (in_array($dep, $obeservaRequires, true)) {
            $errors[] = "Package [{$package}] illegally requires [{$dep}].";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Package boundary validation failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

fwrite(STDOUT, "Package boundaries OK.\n");
