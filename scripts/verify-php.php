<?php
/**
 * Syntax-check every PHP file in the plugin (excluding vendor/).
 *
 * @package OTHello
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$failed = 0;
$checked = 0;

/** @var SplFileInfo $file */
foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    if (
        str_contains($path, '/vendor/')
        || str_contains($path, '/node_modules/')
        || str_contains($path, '/dist/')
        || str_contains($path, '/backups/')
    ) {
        continue;
    }

    $checked++;
    $cmd = 'php -l ' . escapeshellarg($path) . ' 2>&1';
    $output = [];
    $code = 0;
    exec($cmd, $output, $code);
    if ($code !== 0) {
        $failed++;
        fwrite(STDERR, implode("\n", $output) . "\n");
    }
}

fwrite(STDOUT, sprintf("Checked %d PHP files.\n", $checked));

if ($failed > 0) {
    fwrite(STDERR, sprintf("%d file(s) failed php -l.\n", $failed));
    exit(1);
}

fwrite(STDOUT, "PHP syntax OK.\n");
exit(0);
