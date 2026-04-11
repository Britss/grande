<?php $user = auth_user(); ?>
<header class="site-header">
    <div class="container header-row">
        <a class="brand" href="<?= e(url()) ?>">
            <img
                class="brand-logo"
                src="<?= e(url('public/images/grandegologo.png')) ?>"
                alt="Grande. Pandesal + Coffee"
            >
            <span class="brand-mark">Grande.</span>
            <span class="brand-subtitle">Pandesal + Coffee</span>
        </a>

        <nav class="site-nav" aria-label="Primary navigation">
            <a class="<?= route_is('/') ? 'is-active' : '' ?>" href="<?= e(url()) ?>">Home</a>
            <a class="<?= route_is('/about') ? 'is-active' : '' ?>" href="<?= e(url('about')) ?>">About</a>
            <a class="<?= route_is('/menu') ? 'is-active' : '' ?>" href="<?= e(url('menu')) ?>">Menu</a>
            <a class="<?= route_is('/reserve') ? 'is-active' : '' ?>" href="<?= e(url('reserve')) ?>">Reserve</a>
            <a class="<?= route_is('/feedback') ? 'is-active' : '' ?>" href="<?= e(url('feedback')) ?>">Feedback</a>
            <?php if ($user !== null): ?>
                <a class="<?= route_starts_with('/dashboard') ? 'is-active' : '' ?>" href="<?= e(url(auth_dashboard_path())) ?>">Dashboard</a>
                <form class="site-nav__form" method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="site-nav__button site-nav__cta site-nav__logout" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="site-nav__cta <?= route_is('/login') ? 'is-active' : '' ?>" href="<?= e(url('login')) ?>">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
