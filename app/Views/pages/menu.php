<?php $hero = $page['hero']; ?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container">
    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="content-card menu-status-card">
        <?php if (!is_array($user ?? null)): ?>
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
            <p><?= e((string) $cartTotals['item_count']) ?> item(s) selected • PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
            <div class="action-row">
                <a class="button button-primary" href="<?= e(url('cart')) ?>">View Cart</a>
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
        <?php foreach ($page['categories'] as $category): ?>
            <section class="menu-category-block">
                <div class="section-heading">
                    <h2><?= e($category['name']) ?></h2>
                    <p><?= e($category['description']) ?></p>
                </div>

                <div class="content-grid">
                    <?php foreach ($category['items'] as $item): ?>
                        <article class="content-card menu-item-card">
                            <?php if (!empty($item['image_url'])): ?>
                                <?php
                                    $imageUrl = (string) $item['image_url'];
                                    $imageSrc = preg_match('/^https?:\/\//i', $imageUrl) ? $imageUrl : url($imageUrl);
                                ?>
                                <img class="menu-item-card__image" src="<?= e($imageSrc) ?>" alt="<?= e($item['name']) ?>">
                            <?php endif; ?>
                            <h3><?= e($item['name']) ?></h3>
                            <p><?= e($item['description']) ?></p>
                            <div class="price-list">
                                <?php foreach ($item['sizes'] as $size): ?>
                                    <span class="price-pill">
                                        <?= e($size['label']) ?>: PHP <?= e(number_format((float) $size['price'], 2)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <?php if (is_array($user ?? null) && (($user['role'] ?? 'customer') === 'customer')): ?>
                                <form class="menu-order-form" method="post" action="<?= e(url('menu/cart')) ?>">
                                    <?= csrf_field() ?>
                                    <div class="menu-order-grid">
                                        <div class="form-field">
                                            <label for="size-<?= e((string) $item['id']) ?>">Size</label>
                                            <select id="size-<?= e((string) $item['id']) ?>" name="size_id">
                                                <?php foreach ($item['sizes'] as $size): ?>
                                                    <option value="<?= e((string) $size['id']) ?>" <?= $size['is_default'] ? 'selected' : '' ?>>
                                                        <?= e($size['label']) ?> • PHP <?= e(number_format((float) $size['price'], 2)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-field menu-order-qty">
                                            <label for="quantity-<?= e((string) $item['id']) ?>">Qty</label>
                                            <input id="quantity-<?= e((string) $item['id']) ?>" type="number" name="quantity" min="1" max="20" value="1">
                                        </div>
                                    </div>
                                    <button class="button button-primary menu-order-button" type="submit">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <div class="menu-order-guest-note">
                                    <p>Login first to add this item to your cart.</p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
