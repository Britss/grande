<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription ?? 'GrandeGo rewrite in progress.') ?>">
    <title><?= e(($pageTitle ?? 'Grande.') . ' | GrandeGo Rewrite') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <main class="page-shell">
        <?= $content ?>
    </main>

    <?php if (!str_contains((string) ($bodyClass ?? ''), 'dashboard-body')): ?>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    <?php endif; ?>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
