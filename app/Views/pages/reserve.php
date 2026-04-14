<?php $hero = $page['hero']; ?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container reservation-section">
    <?php $loggedInCustomer = is_array($user ?? null) && (($user['role'] ?? 'customer') === 'customer'); ?>

    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <?php if (!auth_check()): ?>
        <div class="alert alert-error">
            You must be logged in to make a reservation.
            <a href="<?= e(url('login')) ?>">Login now</a>.
        </div>
    <?php elseif (!$loggedInCustomer): ?>
        <div class="alert alert-info">Only customer accounts can place reservation orders in this phase.</div>
    <?php elseif (($cartTotals['item_count'] ?? 0) === 0): ?>
        <div class="alert alert-info">
            Add at least one item from the
            <a href="<?= e(url('menu')) ?>">menu</a>
            before making a reservation.
        </div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card reservation-form-card">
            <h2>Make a Reservation</h2>
            <p><?= e($page['form_intro']) ?></p>
            <form class="stack-form" action="<?= e(url('reserve')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <?php foreach ($page['form_fields'] as $field): ?>
                        <div class="form-field <?= $field['name'] === 'guests' ? 'full-width' : '' ?>">
                            <label for="<?= e($field['name']) ?>"><?= e($field['label']) ?></label>
                            <input
                                id="<?= e($field['name']) ?>"
                                name="<?= e($field['name']) ?>"
                                type="<?= e($field['type']) ?>"
                                class="form-control <?= has_error($field['name']) ? 'is-invalid' : '' ?>"
                                <?php if (!empty($field['min'])): ?>min="<?= e($field['min']) ?>"<?php endif; ?>
                                <?php if (!empty($field['max'])): ?>max="<?= e($field['max']) ?>"<?php endif; ?>
                                value="<?=
                                    e((string) match ($field['name']) {
                                        'date' => old('date', ''),
                                        'time' => old('time', ''),
                                        'guests' => old('guests', $field['value'] ?? '1'),
                                        'first_name' => old('first_name', $user['first_name'] ?? ''),
                                        'last_name' => old('last_name', $user['last_name'] ?? ''),
                                        'email' => old('email', $user['email'] ?? ''),
                                        'phone' => old('phone', $user['phone'] ?? ''),
                                        default => old($field['name'], $field['value'] ?? ''),
                                    })
                                ?>"
                                <?= (!$loggedInCustomer || ($cartTotals['item_count'] ?? 0) === 0) ? 'disabled' : '' ?>
                            >
                            <?php if ($message = field_error($field['name'])): ?>
                                <small class="field-error"><?= e((string) $message) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="button button-primary" <?= (!$loggedInCustomer || ($cartTotals['item_count'] ?? 0) === 0) ? 'disabled' : '' ?>>
                    Reserve Table
                </button>
            </form>
        </div>

        <aside class="reservation-side-panel" aria-labelledby="reservation-guide-title">
            <div class="reservation-side-panel__intro">
                <h2 id="reservation-guide-title">Before You Reserve</h2>
                <p>Keep these details ready so your table and linked order can move through smoothly.</p>
            </div>
            <?php if ($loggedInCustomer): ?>
                <article class="reservation-example reservation-cart-panel">
                    <span>Cart</span>
                    <div>
                        <h3>Cart Ready</h3>
                    <?php if ($cartItems !== []): ?>
                        <p><?= e((string) $cartTotals['item_count']) ?> item(s) selected • PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
                        <div class="cart-summary-list">
                            <?php foreach (array_slice($cartItems, 0, 3) as $item): ?>
                                <div class="cart-summary-item">
                                    <div>
                                        <strong><?= e($item['item_name']) ?></strong>
                                        <p><?= e($item['size']) ?> • Qty <?= e((string) $item['quantity']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="action-row reserve-sidebar-actions">
                            <a class="button button-primary" href="<?= e(url('cart?from=reservation')) ?>">Review Cart</a>
                            <a class="button button-secondary" href="<?= e(url('menu')) ?>">Add More Items</a>
                        </div>
                    <?php else: ?>
                        <p>Your cart is empty. Build it from the menu before reserving.</p>
                        <a class="button button-primary" href="<?= e(url('menu')) ?>">Go to Menu</a>
                    <?php endif; ?>
                    </div>
                </article>
            <?php endif; ?>

            <div class="reservation-examples" aria-label="Reservation reminders">
            <?php foreach ($page['sidebar_cards'] as $card): ?>
                <article class="reservation-example">
                    <span><?= e((string) ($card['label'] ?? $card['title'])) ?></span>
                    <div>
                        <h3><?= e($card['title']) ?></h3>
                        <p><?= e($card['body']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        </aside>
    </div>
</section>

<section class="page-section container reservation-benefits">
    <h2>Why Reserve at Grande?</h2>
    <div class="content-grid">
        <?php foreach ($page['amenities'] as $amenity): ?>
            <article class="content-card">
                <h3><?= e(is_array($amenity) ? $amenity['title'] : $amenity) ?></h3>
                <p><?= e(is_array($amenity) ? $amenity['body'] : 'This item remains part of the reservation page because it supports the same customer decision flow as the current site.') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="about-invitation reservation-invitation">
    <div class="container about-invitation__layout" data-reveal>
        <div class="about-invitation__copy">
            <h2><?= e($page['cta']['title']) ?></h2>
            <p><?= e($page['cta']['body']) ?></p>
        </div>

        <div class="about-invitation__actions">
            <a class="button button-primary" href="<?= e(url('menu')) ?>">View Menu</a>
            <a class="button button-secondary" href="<?= e(url('cart')) ?>">Review Cart</a>
        </div>
    </div>
</section>
