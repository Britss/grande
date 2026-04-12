<section class="home-hero">
    <div class="container home-hero__content">
        <p class="home-hero__eyebrow">Grande. Pan De Sal + Coffee</p>
        <h1>Freshly Baked, Freshly Brewed - EVERY. SINGLE. DAY.</h1>
        <p class="home-hero__lead">
            Pandesal, coffee, and a cozy stop in Sindalan.
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
                <span>Fresh Pandesal</span>
            </div>
            <div class="hero-highlights__item">
                <img src="<?= e(url('public/icons/cup.png')) ?>" alt="" aria-hidden="true">
                <span>Quality Coffee</span>
            </div>
        </div>
    </div>
</section>

<section class="home-band">
    <div class="container home-band__row">
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/pin.png')) ?>" alt="" aria-hidden="true">
            <p>Beside Puregold Sindalan, Pampanga</p>
        </div>
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/star1.png')) ?>" alt="" aria-hidden="true">
            <p>Warm bites and coffee anytime</p>
        </div>
        <div class="home-band__item">
            <img src="<?= e(url('public/icons/telephone.png')) ?>" alt="" aria-hidden="true">
            <p>Dine in, take out, or reserve</p>
        </div>
    </div>
</section>

<section class="home-middle" aria-labelledby="home-middle-title">
    <div class="container home-middle__layout">
        <div class="home-middle__copy">
            <p class="home-middle__eyebrow">Fresh Every Hour</p>
            <h2 id="home-middle-title">Pan de sal, coffee, and comfort any time of day.</h2>
            <p>
                Drop by in Sindalan for warm bread, premium coffee, and a table ready whenever the craving hits.
            </p>

            <div class="home-middle__actions">
                <a class="button button-primary" href="<?= e(url('menu')) ?>">View Menu</a>
                <a class="button button-secondary" href="<?= e(url('reserve')) ?>">Reserve a Table</a>
            </div>
        </div>

        <div class="home-middle__showcase" aria-label="Grande favorites">
            <article class="home-product home-product--large">
                <img src="<?= e(url('public/images/menu-items/classic_pan_de_sal.png')) ?>" alt="Fresh classic pan de sal">
                <div class="home-product__content">
                    <h3>Fresh Pan De Sal</h3>
                    <p>Soft, warm, and baked for breakfast, merienda, and late-night cravings.</p>
                </div>
            </article>

            <article class="home-product home-product--coffee">
                <img src="<?= e(url('public/images/menu-items/cappuccino.png')) ?>" alt="Grande cappuccino">
                <div class="home-product__content">
                    <h3>Premium Coffee</h3>
                    <p>Freshly brewed for dine in or take out.</p>
                </div>
            </article>

            <article class="home-product home-product--open">
                <img src="<?= e(url('public/images/menu-items/pandesal_fest_bundle.jpg')) ?>" alt="Grande pan de sal bundle">
                <div class="home-product__content">
                    <h3>Open 24/7</h3>
                    <p>Beside Puregold Sindalan, ready whenever you are.</p>
                </div>
            </article>
        </div>
    </div>
</section>