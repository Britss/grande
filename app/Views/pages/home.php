<section class="home-hero">
    <div class="container home-hero__content">
        <p class="home-hero__eyebrow">Grande. Pandesal + Coffee</p>
        <h1>Freshly Baked, Freshly Brewed - EVERY. SINGLE. DAY.</h1>
        <p class="home-hero__lead">
            Pan De Sal &amp; Premium Coffee - Open 24/7 in Sindalan
        </p>

        <div class="home-hero__actions">
            <?php foreach ($page['cta'] as $cta): ?>
                <a class="button <?= $cta['label'] === 'View Menu' ? 'button-primary' : 'button-secondary' ?>" href="<?= e($cta['href']) ?>"><?= e($cta['label']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="hero-highlights">
            <div class="hero-highlights__item">
                <img src="<?= e(url('public/icons/clock.png')) ?>" alt="" aria-hidden="true">
                <span>Open 24/7</span>
            </div>
            <div class="hero-highlights__item">
                <img src="<?= e(url('public/icons/bread.png')) ?>" alt="" aria-hidden="true">
                <span>Fresh Pan De Sal</span>
            </div>
            <div class="hero-highlights__item">
                <img src="<?= e(url('public/icons/cup.png')) ?>" alt="" aria-hidden="true">
                <span>Premium Coffee</span>
            </div>
        </div>
    </div>
</section>

<section class="home-band">
    <div class="container home-band__row">
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/pin.png')) ?>" alt="" aria-hidden="true">
            <p>Beside Puregold, Sindalan, San Fernando, Pampanga</p>
        </div>
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/star1.png')) ?>" alt="" aria-hidden="true">
            <p>Warm cafe atmosphere with local bakery identity</p>
        </div>
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/telephone.png')) ?>" alt="" aria-hidden="true">
            <p>Built to preserve the current GrandeGo customer feel</p>
        </div>
    </div>
</section>

<section class="page-section container">
    <div class="content-grid home-detail-grid">
        <div class="content-card content-card--feature">
            <p class="eyebrow">Current Step</p>
            <h2>Architecture first, design with intent.</h2>
            <p><?= e($page['intro']) ?></p>
        </div>

        <div class="home-note">
            <p class="eyebrow">Visual Direction</p>
            <h2>Closer to GrandeGo without dragging in the old codebase.</h2>
            <p>
                This sample keeps the rewrite structure but shifts the first impression toward the cafe tone of the original site: warmer palette, stronger hero, tighter navigation, and more deliberate spacing.
            </p>
        </div>
    </div>
</section>
