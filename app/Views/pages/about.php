<section class="about-poster">
    <div class="container about-poster__layout">
        <div class="about-poster__copy" data-reveal>
            <h1><?= e($page['hero']['title']) ?></h1>
            <?php if (!empty($page['hero']['lead'])): ?>
                <p class="about-poster__lead"><?= e($page['hero']['lead']) ?></p>
            <?php endif; ?>
            <p class="about-poster__body"><?= e($page['hero']['body']) ?></p>

            <?php if (!empty($page['hero']['actions'])): ?>
                <div class="about-poster__actions">
                    <?php foreach ($page['hero']['actions'] as $action): ?>
                        <a class="button <?= $action['label'] === 'View Menu' ? 'button-primary' : 'button-secondary' ?>" href="<?= e($action['href']) ?>">
                            <?= e($action['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="about-poster__visual" data-reveal>
            <figure class="about-poster__image about-poster__image--primary">
                <img src="<?= e($page['hero']['image']['src']) ?>" alt="<?= e($page['hero']['image']['alt']) ?>">
            </figure>

            <figure class="about-poster__image about-poster__image--secondary">
                <img src="<?= e($page['hero']['secondary_image']['src']) ?>" alt="<?= e($page['hero']['secondary_image']['alt']) ?>">
            </figure>

            <p class="about-poster__framing"><?= e($page['hero']['framing']) ?></p>
        </div>

        <?php if (!empty($page['hero']['quick_facts'])): ?>
            <div class="about-poster__facts" aria-label="Grande quick facts">
                <?php foreach ($page['hero']['quick_facts'] as $fact): ?>
                    <article class="about-poster__fact" data-reveal>
                        <p class="about-poster__fact-label"><?= e($fact['label']) ?></p>
                        <p class="about-poster__fact-detail"><?= e($fact['detail']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="about-timeline" aria-labelledby="about-timeline-title">
    <div class="container">
        <div class="about-timeline__intro" data-reveal>
            <h2 id="about-timeline-title">Grande feels different by the hour, but it stays familiar all through it.</h2>
        </div>

        <div class="about-timeline__track">
            <?php foreach ($page['timeline_moments'] as $index => $moment): ?>
                <article class="about-moment" data-reveal style="--about-delay: <?= e((string) ($index * 120)) ?>ms;">
                    <div class="about-moment__meta">
                        <p class="about-moment__time"><?= e($moment['time']) ?></p>
                    </div>

                    <div class="about-moment__content">
                        <div class="about-moment__copy">
                            <h3><?= e($moment['title']) ?></h3>
                            <p><?= e($moment['body']) ?></p>
                        </div>

                        <figure class="about-moment__image">
                            <img src="<?= e($moment['image']['src']) ?>" alt="<?= e($moment['image']['alt']) ?>">
                        </figure>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="about-proofbook" aria-labelledby="about-proofbook-title">
    <div class="container about-proofbook__layout">
        <div class="about-proofbook__intro" data-reveal>
            <h2 id="about-proofbook-title"><?= e($page['proof_points']['intro']['title']) ?></h2>
            <p><?= e($page['proof_points']['intro']['body']) ?></p>
        </div>

        <div class="about-proofbook__list">
            <?php foreach ($page['proof_points'] as $key => $point): ?>
                <?php if (!is_int($key)) {
                    continue;
                } ?>
                <article class="about-proofbook__item" data-reveal>
                    <div class="about-proofbook__icon">
                        <img src="<?= e($point['icon']) ?>" alt="" aria-hidden="true">
                    </div>

                    <div class="about-proofbook__copy">
                        <p class="about-proofbook__kicker"><?= e($point['kicker']) ?></p>
                        <h3><?= e($point['title']) ?></h3>
                        <p><?= e($point['body']) ?></p>
                    </div>

                    <figure class="about-proofbook__image">
                        <img src="<?= e($point['image']['src']) ?>" alt="<?= e($point['image']['alt']) ?>">
                    </figure>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="about-invitation">
    <div class="container about-invitation__layout" data-reveal>
        <div class="about-invitation__copy">
            <h2><?= e($page['closing_cta']['title']) ?></h2>
            <p><?= e($page['closing_cta']['body']) ?></p>
        </div>

        <div class="about-invitation__actions">
            <?php foreach ($page['closing_cta']['actions'] as $action): ?>
                <a class="button <?= $action['label'] === 'View Menu' ? 'button-primary' : 'button-secondary' ?>" href="<?= e($action['href']) ?>">
                    <?= e($action['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
