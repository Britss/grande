<?php $hero = $page['hero']; ?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container">
    <?php if ($status = flash('status')): ?>
        <div class="menu-cart-popup" data-menu-cart-popup>
            <div class="menu-cart-popup__backdrop" data-menu-cart-popup-close></div>
            <div class="menu-cart-popup__card" role="dialog" aria-modal="true" aria-labelledby="menu-cart-popup-title" aria-describedby="menu-cart-popup-message">
                <div class="menu-cart-popup__status" aria-hidden="true">
                    <span class="menu-cart-popup__status-icon">+</span>
                    <span class="menu-cart-popup__status-label">Cart updated</span>
                </div>
                <div class="menu-cart-popup__copy">
                    <h2 id="menu-cart-popup-title">Added to Cart</h2>
                    <p id="menu-cart-popup-message"><?= e((string) $status) ?></p>
                </div>
                <button type="button" class="menu-cart-popup__close" data-menu-cart-popup-close>Exit</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <?php $isGuest = !is_array($user ?? null); ?>
    <?php $isCustomer = is_array($user ?? null) && (($user['role'] ?? 'customer') === 'customer'); ?>
    <div class="menu-status-card<?= ($isGuest || $isCustomer) ? ' menu-status-card--floating' : '' ?>"<?= $isCustomer ? ' data-menu-cart-summary' : '' ?>>
        <?php if ($isGuest): ?>
            <h2>Login Before Building Your Cart</h2>
            <p>Add your drinks and bakery picks after logging in so your cart stays tied to your account.</p>
            <div class="action-row">
                <a class="button button-primary" href="<?= e(url('login')) ?>">Login to Start</a>
            </div>
        <?php elseif (($user['role'] ?? 'customer') !== 'customer'): ?>
            <h2>Customer Ordering Only</h2>
            <p>The current ordering flow is enabled for customer accounts only.</p>
        <?php else: ?>
            <h2>Your Cart</h2>
            <p><span data-menu-cart-count><?= e((string) $cartTotals['item_count']) ?></span> item(s) selected &middot; PHP <span data-menu-cart-subtotal><?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></span></p>
            <div class="action-row">
                <a class="button button-primary" href="<?= e(url('cart')) ?>">View Cart & Checkout</a>
                <a class="button button-secondary" href="<?= e(url('cart?from=reservation')) ?>">Reserve With Cart</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($page['categories'] === []): ?>
        <div class="content-card">
            <h2>Menu Unavailable</h2>
            <p>The menu catalog has not been loaded yet.</p>
        </div>
    <?php else: ?>
        <div class="menu-controls" data-menu-filter>
            <div class="form-field menu-search-field">
                <label for="menu-search">Search menu</label>
                <div class="menu-search-box">
                    <input
                        id="menu-search"
                        class="form-control"
                        type="search"
                        placeholder="Search drinks, bread, pastries"
                        autocomplete="off"
                        data-menu-search
                    >
                    <img src="<?= e(url('public/icons/magnifying-glass.png')) ?>" alt="" aria-hidden="true">
                </div>
            </div>

            <div class="menu-category-tabs" aria-label="Menu categories">
                <button class="menu-category-tab is-active" type="button" data-menu-category="all">All</button>
                <?php foreach ($page['categories'] as $category): ?>
                    <button class="menu-category-tab" type="button" data-menu-category="<?= e(strtolower((string) ($category['key'] ?? $category['name']))) ?>">
                        <?= e($category['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="content-card menu-empty-state" data-menu-empty hidden>
            <h2>No menu items found</h2>
            <p>Try another search term or choose a different category.</p>
        </div>

        <?php foreach ($page['categories'] as $category): ?>
            <section class="menu-category-block" data-menu-category-block>
                <div class="section-heading">
                    <h2><?= e($category['name']) ?></h2>
                    <p><?= e($category['description']) ?></p>
                </div>

                <div class="menu-grid">
                    <?php foreach ($category['items'] as $item): ?>
                        <?php
                            $sizeSearchText = implode(' ', array_map(
                                static fn (array $size): string => (string) ($size['label'] ?? ''),
                                $item['sizes']
                            ));
                            $filterText = strtolower(trim(
                                ($item['name'] ?? '') . ' ' .
                                ($item['category'] ?? '') . ' ' .
                                ($item['description'] ?? '') . ' ' .
                                $sizeSearchText
                            ));
                        ?>
                        <article
                            class="menu-item-card"
                            data-menu-item
                            data-menu-category="<?= e(strtolower((string) ($item['category'] ?? $category['key'] ?? $category['name']))) ?>"
                            data-menu-text="<?= e($filterText) ?>"
                        >
                            <?php if (!empty($item['image_url'])): ?>
                                <?php
                                    $imageUrl = (string) $item['image_url'];
                                    $imageSrc = preg_match('/^https?:\/\//i', $imageUrl) ? $imageUrl : url($imageUrl);
                                ?>
                                <img class="menu-item-card__image" src="<?= e($imageSrc) ?>" alt="<?= e($item['name']) ?>">
                            <?php endif; ?>
                            <div class="menu-item-card__body">
                                <h3><?= e($item['name']) ?></h3>
                                <p><?= e($item['description']) ?></p>

                                <?php if (is_array($user ?? null) && (($user['role'] ?? 'customer') === 'customer')): ?>
                                    <form class="menu-order-form" method="post" action="<?= e(url('menu/cart')) ?>">
                                    <?= csrf_field() ?>
                                    <div class="menu-order-grid">
                                        <div class="form-field menu-size-field">
                                            <span class="menu-control-label">Size</span>
                                            <div class="menu-size-options" role="radiogroup" aria-label="Choose size for <?= e($item['name']) ?>">
                                                <?php foreach ($item['sizes'] as $size): ?>
                                                    <label class="menu-size-option">
                                                        <input
                                                            type="radio"
                                                            name="size_id"
                                                            value="<?= e((string) $size['id']) ?>"
                                                            <?= $size['is_default'] ? 'checked' : '' ?>
                                                        >
                                                        <span>
                                                        <?= e($size['label']) ?> &middot; PHP <?= e(number_format((float) $size['price'], 2)) ?>
                                                        </span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="form-field menu-order-qty">
                                            <label for="quantity-<?= e((string) $item['id']) ?>">Qty</label>
                                            <div class="menu-qty-control">
                                                <button type="button" data-quantity-step="-1" aria-label="Decrease quantity for <?= e($item['name']) ?>">-</button>
                                                <input id="quantity-<?= e((string) $item['id']) ?>" type="number" name="quantity" min="1" max="20" value="1">
                                                <button type="button" data-quantity-step="1" aria-label="Increase quantity for <?= e($item['name']) ?>">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="button button-primary menu-order-button" type="submit">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <div class="menu-order-guest-note">
                                        <p>Login first to add this item to your cart.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
