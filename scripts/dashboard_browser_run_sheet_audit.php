<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$allowPending = in_array('--allow-pending', $argv, true);
$root = realpath(__DIR__ . '/..');

if ($root === false) {
    fwrite(STDERR, "Could not resolve repository root.\n");
    exit(1);
}

$runSheetPath = $root . '/docs/dashboard-browser-parity-run-sheet.md';

if (!is_file($runSheetPath)) {
    fwrite(STDERR, "Missing browser parity run sheet: {$runSheetPath}\n");
    exit(1);
}

$contents = (string) file_get_contents($runSheetPath);
$failures = [];
$warnings = [];
$validStatuses = ['Pass', 'Pass with note', 'Fail'];
$pendingStatus = 'Not run';

preg_match_all('/^\| (Customer|Employee|Admin) \| ([^|]+) \| ([^|]+) \| ([^|]+) \| ([^|]+) \|$/m', $contents, $matrixRows, PREG_SET_ORDER);

if (count($matrixRows) !== 3) {
    $failures[] = 'Viewport Matrix must contain exactly one Customer, Employee, and Admin row.';
}

$seenRoles = [];

foreach ($matrixRows as $row) {
    $role = $row[1];
    $seenRoles[] = $role;

    foreach (['1440px' => $row[2], '1280px' => $row[3], '768px' => $row[4], '390px' => $row[5]] as $viewport => $rawStatus) {
        $status = trim($rawStatus);

        if ($status === $pendingStatus) {
            if (!$allowPending) {
                $failures[] = "{$role} {$viewport}: still marked Not run.";
            }

            continue;
        }

        if (!in_array($status, $validStatuses, true)) {
            $failures[] = "{$role} {$viewport}: invalid status `{$status}`. Use Pass, Pass with note, Fail, or Not run.";
        }
    }
}

foreach (['Customer', 'Employee', 'Admin'] as $role) {
    if (!in_array($role, $seenRoles, true)) {
        $failures[] = "Viewport Matrix is missing the {$role} row.";
    }
}

foreach (['Browser and version', 'Date run', 'Tester', 'Overall result', 'Follow-up tasks opened'] as $field) {
    if (!preg_match('/^- ' . preg_quote($field, '/') . ':\s*(.+)$/m', $contents, $match)) {
        $failures[] = "Completion Summary is missing `{$field}`.";
        continue;
    }

    if (!$allowPending && trim($match[1]) === '') {
        $failures[] = "Completion Summary field `{$field}` is blank.";
    }
}

preg_match_all('/^\| (Customer|Employee|Admin) \| (1440px|1280px|768px|390px) \|([^|]*)\|([^|]*)\|$/m', $contents, $screenshotRows, PREG_SET_ORDER);

if (count($screenshotRows) !== 12) {
    $failures[] = 'Screenshot Notes must contain all 12 role/viewport rows.';
}

foreach ($screenshotRows as $row) {
    $role = $row[1];
    $viewport = $row[2];
    $screenshot = trim($row[3]);
    $notes = trim($row[4]);

    if ($allowPending) {
        continue;
    }

    if ($screenshot === '' && $notes === '') {
        $warnings[] = "{$role} {$viewport}: no screenshot path or note was recorded.";
        continue;
    }

    if ($screenshot === '') {
        continue;
    }

    $screenshotPath = str_replace('\\', '/', $screenshot);

    if (preg_match('/^[A-Za-z]:\//', $screenshotPath) === 1 || str_starts_with($screenshotPath, '/')) {
        $resolvedScreenshotPath = $screenshotPath;
    } else {
        $resolvedScreenshotPath = $root . '/' . ltrim($screenshotPath, '/');
    }

    if (!is_file($resolvedScreenshotPath)) {
        $failures[] = "{$role} {$viewport}: screenshot file was not found at `{$screenshot}`.";
    }
}

if ($warnings !== []) {
    fwrite(STDERR, "Dashboard browser run sheet warnings:\n");

    foreach ($warnings as $warning) {
        fwrite(STDERR, "- {$warning}\n");
    }

    fwrite(STDERR, "\n");
}

if ($failures !== []) {
    fwrite(STDERR, "Dashboard browser run sheet audit failed:\n");

    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }

    fwrite(STDERR, "\nRun with --allow-pending only when validating the template before the manual browser pass is complete.\n");
    exit(1);
}

if ($allowPending) {
    echo "Dashboard browser run sheet template is valid; pending viewport entries are allowed for handoff.\n";
    exit(0);
}

echo "Dashboard browser run sheet is complete enough to copy confirmed findings into the dated QA record.\n";
