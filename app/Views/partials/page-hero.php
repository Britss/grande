<section class="page-hero">
    <div class="container">
        <p class="eyebrow"><?= e($hero['eyebrow'] ?? '') ?></p>
        <h1><?= e($hero['title'] ?? '') ?></h1>
        <?php if (!empty($hero['subtitle'])): ?>
            <p class="lead"><?= e($hero['subtitle']) ?></p>
        <?php endif; ?>
    </div>
</section>
