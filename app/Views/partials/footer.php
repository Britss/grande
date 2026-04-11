<?php $sharedSite = \App\Support\PublicContent::shared(); ?>

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Grande. Pandesal + Coffee. All rights reserved.</p>
        <p class="footer-address">
            Beside Puregold, In front of St. Anthony's Drug Store<br>
            Sindalan, San Fernando, Pampanga
        </p>
        <p class="footer-contact-row">
            <span class="footer-inline-item">
                <img src="<?= e(url('public/icons/telephone.png')) ?>" alt="" class="footer-icon" aria-hidden="true">
                <span><?= e($sharedSite['contact']['phone']) ?></span>
            </span>
            <span class="footer-inline-separator">|</span>
            <span class="footer-inline-item">
                <img src="<?= e(url('public/icons/email.png')) ?>" alt="" class="footer-icon" aria-hidden="true">
                <span><?= e($sharedSite['contact']['email']) ?></span>
            </span>
        </p>
        <p class="footer-follow-label">Follow Us:</p>
        <div class="social-links">
            <?php foreach ($sharedSite['social_links'] as $socialLink): ?>
                <?php
                $iconName = strtolower($socialLink['label']) === 'tiktok'
                    ? 'tik-tok'
                    : strtolower($socialLink['label']);
                ?>
                <a href="<?= e($socialLink['url']) ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?= e(url('public/icons/' . $iconName . '.png')) ?>" alt="" class="footer-icon" aria-hidden="true">
                    <span><?= e($socialLink['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="footer-hours">
            <img src="<?= e(url('public/icons/clock.png')) ?>" alt="" class="footer-icon" aria-hidden="true">
            <span>OPEN 24/7 - Always Fresh, Always Here for You</span>
        </p>
    </div>
</footer>
