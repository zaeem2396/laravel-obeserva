#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$docsDir = $root.'/docs';

$requiredFiles = [
    'INSTALLATION.md',
    'RELEASE.md',
    'ARCHITECTURE.md',
    'PACKAGES.md',
    'UPGRADE.md',
    'API_STABILITY.md',
    'BENCHMARKS.md',
    'SCOUT_INTEGRATION.md',
    'posts/v1.0.0-stable-release.md',
];

$errors = [];

foreach ($requiredFiles as $relativePath) {
    $path = $docsDir.'/'.$relativePath;
    if (! is_file($path)) {
        $errors[] = "Missing required doc: docs/{$relativePath}";
    }
}

$markdownFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($docsDir, FilesystemIterator::SKIP_DOTS),
);

foreach ($markdownFiles as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }

    $content = file_get_contents($file->getPathname());
    if ($content === false) {
        $errors[] = "Unable to read: {$file->getPathname()}";

        continue;
    }

    if (preg_match_all('/```php\n(.*?)\n```/s', $content, $matches) === 0) {
        continue;
    }

    foreach ($matches[1] as $index => $phpBlock) {
        $snippet = trim($phpBlock);
        if ($snippet === '' || str_starts_with($snippet, '...')) {
            continue;
        }

        $wrapped = "<?php\n\n".$snippet;
        $tokens = @token_get_all($wrapped);

        if ($tokens === false) {
            $errors[] = sprintf(
                'Invalid PHP in %s block #%d',
                str_replace($root.'/', '', $file->getPathname()),
                $index + 1,
            );
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Documentation validation failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }

    exit(1);
}

fwrite(STDOUT, "Documentation validation passed.\n");
