<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$date = date('Y-m-d');

foreach ($argv as $argument) {
    if (str_starts_with($argument, '--date=')) {
        $date = trim(substr($argument, 7));
    }
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    fwrite(STDERR, "Invalid --date value. Use YYYY-MM-DD.\n");
    exit(1);
}

$root = realpath(__DIR__ . '/..');

if ($root === false) {
    fwrite(STDERR, "Could not resolve repository root.\n");
    exit(1);
}

$artifactDir = $root . '/docs/dashboard-browser-qa-artifacts/' . $date;
$roles = ['customer', 'employee', 'admin'];
$viewports = ['1440px', '1280px', '768px', '390px'];

if (!is_dir($artifactDir) && !mkdir($artifactDir, 0775, true)) {
    fwrite(STDERR, "Could not create artifact directory: {$artifactDir}\n");
    exit(1);
}

$notesPath = $artifactDir . '/notes.md';
$lines = [
    '# Dashboard Browser QA Artifacts - ' . $date,
    '',
    'Use these filenames when filling `docs/dashboard-browser-parity-run-sheet.md`.',
    'Keep screenshots in this directory so the run sheet can reference stable paths.',
    '',
    '| Role | Viewport | Screenshot File | Notes |',
    '| --- | --- | --- | --- |',
];

foreach ($roles as $role) {
    foreach ($viewports as $viewport) {
        $filename = "{$role}-dashboard-{$viewport}.png";
        $lines[] = '| ' . ucfirst($role) . ' | ' . $viewport . ' | ' . $filename . ' |  |';
    }
}

$lines[] = '';
$lines[] = 'Interaction notes:';
$lines[] = '';
$lines[] = '- Mouse navigation:';
$lines[] = '- Keyboard navigation:';
$lines[] = '- Mobile/touch navigation:';
$lines[] = '- Modal behavior:';
$lines[] = '- Filters/forms/AJAX behavior:';
$lines[] = '- Overflow/toast/table behavior:';

if (!file_exists($notesPath)) {
    file_put_contents($notesPath, implode("\n", $lines) . "\n");
}

echo "Dashboard browser QA artifact directory is ready:\n";
echo str_replace('\\', '/', $artifactDir) . "\n\n";
echo "Expected screenshot filenames:\n";

foreach ($roles as $role) {
    foreach ($viewports as $viewport) {
        echo "- {$role}-dashboard-{$viewport}.png\n";
    }
}

echo "\nNotes template:\n";
echo str_replace('\\', '/', $notesPath) . "\n";
