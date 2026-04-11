<?php $hero = $page['hero']; ?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container">
    <div class="content-card">
        <h2><?= e($page['story']['title']) ?></h2>
        <?php foreach ($page['story']['paragraphs'] as $paragraph): ?>
            <p><?= e($paragraph) ?></p>
        <?php endforeach; ?>
    </div>

    <div class="content-grid three-up">
        <?php foreach ($page['pillars'] as $pillar): ?>
            <article class="content-card">
                <h2><?= e($pillar['title']) ?></h2>
                <p><?= e($pillar['body']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="page-section container">
    <h2>Why Choose Grande?</h2>
    <div class="content-grid">
        <?php foreach ($page['features'] as $feature): ?>
            <article class="content-card">
                <h3><?= e($feature['title']) ?></h3>
                <p><?= e($feature['body']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="page-section container">
    <div class="content-card">
        <h2><?= e($page['team']['title']) ?></h2>
        <p><?= e($page['team']['body']) ?></p>
    </div>

    <div class="content-card cta-card">
        <h2><?= e($page['cta']['title']) ?></h2>
        <p><?= e($page['cta']['body']) ?></p>
        <div class="action-row">
            <a class="button button-primary" href="<?= e(url('menu')) ?>">View Menu</a>
            <a class="button button-secondary" href="<?= e(url('reserve')) ?>">Reserve a Table</a>
        </div>
    </div>
</section>
