<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription ?? 'Grande. Pandesal + Coffee in Sindalan.') ?>">
    <title><?= e(($pageTitle ?? 'Home') . ' | Grande. Pan De Sal + Coffee') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <main class="page-shell">
        <?= $content ?>
    </main>

    <?php
        $assistantPaths = ['/', '/about', '/menu', '/reserve', '/feedback', '/cart', '/checkout', '/reservation-checkout'];
        $showAssistant = !str_contains((string) ($bodyClass ?? ''), 'dashboard-body')
            && in_array(request_path(), $assistantPaths, true);
    ?>

    <?php if (!str_contains((string) ($bodyClass ?? ''), 'dashboard-body')): ?>
        <?php if ($showAssistant): ?>
            <?php require __DIR__ . '/../partials/assistant-widget.php'; ?>
        <?php endif; ?>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    <?php endif; ?>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
