<?php

/**
 * Post-install script for laravel-dynamic-helpers package
 * Automatically adds IDE helper file entries to .gitignore
 * Runs once on `composer install` or `composer update`
 */

// Get the root directory (where composer.json is, typically the app root)
$baseDir = dirname(__DIR__, 4);
$gitignorePath = $baseDir . '/.gitignore';

if (! file_exists($gitignorePath)) {
    exit(0);
}

$gitignoreContent = file_get_contents($gitignorePath);
$entriesToAdd = [
    '_ide_helper.php',
    '.phpstorm.meta.php',
];

$lines = array_filter(array_map('trim', explode("\n", $gitignoreContent)));
$needsUpdate = false;

foreach ($entriesToAdd as $entry) {
    if (! in_array($entry, $lines, true)) {
        $lines[] = $entry;
        $needsUpdate = true;
    }
}

if ($needsUpdate) {
    file_put_contents($gitignorePath, implode("\n", $lines) . "\n");
}
