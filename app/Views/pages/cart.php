<section class="page-section container">
    <p class="eyebrow">Cart</p>
    <h1>Your Bread and Coffee Cart</h1>
    <p class="lead">Review your bakery picks and drinks before checkout.</p>

    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <?php if ($cartItems === []): ?>
        <div class="content-card cart-empty-card">
            <span class="cart-empty-card__icon" aria-hidden="true">+</span>
            <h2>Your cart is empty.</h2>
            <p>Add pandesal, coffee, pastries, or tea from the menu to start your order.</p>
            <div class="action-row">
                <a class="button button-primary" href="<?= e(url('menu')) ?>">Continue Shopping</a>
            </div>
        </div>
    <?php else: ?>
        <div class="split-layout">
            <div class="content-card cart-items-card">
                <div class="cart-card-heading">
                    <div>
                        <span class="cart-card-kicker">Selected items</span>
                        <h2>Cart Items</h2>
                    </div>
                    <span class="cart-count-pill"><?= e((string) $cartTotals['item_count']) ?> item(s)</span>
                </div>
                <div class="cart-summary-list">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-summary-item">
                            <div class="cart-summary-copy">
                                <strong><?= e($item['item_name']) ?></strong>
                                <p><?= e($item['size']) ?> &middot; PHP <?= e(number_format((float) $item['item_price'], 2)) ?> each</p>
                            </div>
                            <div class="cart-summary-actions">
                                <form method="post" action="<?= e(url('cart/update')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="cart_item_id" value="<?= e((string) $item['id']) ?>">
                                    <input type="hidden" name="redirect_to" value="<?= e($fromReservation ? '/cart?from=reservation' : '/cart') ?>">
                                    <label class="sr-only" for="cart-quantity-<?= e((string) $item['id']) ?>">Quantity for <?= e($item['item_name']) ?></label>
                                    <input id="cart-quantity-<?= e((string) $item['id']) ?>" type="number" name="quantity" min="0" max="20" value="<?= e((string) $item['quantity']) ?>">
                                    <button type="submit" class="button button-secondary cart-action-button">Update</button>
                                </form>
                                <form method="post" action="<?= e(url('cart/remove')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="cart_item_id" value="<?= e((string) $item['id']) ?>">
                                    <input type="hidden" name="redirect_to" value="<?= e($fromReservation ? '/cart?from=reservation' : '/cart') ?>">
                                    <button type="submit" class="button button-secondary cart-action-button">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="stack-sidebar">
                <article class="content-card cart-total-card">
                    <span class="cart-card-kicker">Next step</span>
                    <h3>Order Summary</h3>
                    <p class="cart-summary-meta"><?= e((string) $cartTotals['item_count']) ?> item(s) ready for <?= $fromReservation ? 'reservation' : 'checkout' ?></p>
                    <p class="cart-summary-total">PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
                    <p class="cart-summary-note">Freshly brewed drinks and everyday bread favorites are prepared for pickup after checkout confirmation.</p>
                    <div class="action-row cart-sidebar-actions">
                        <?php if ($fromReservation): ?>
                            <a class="button button-primary" href="<?= e(url('reserve')) ?>">Proceed to Reservation</a>
                            <a class="button button-secondary" href="<?= e(url('menu')) ?>">Continue Shopping</a>
                        <?php else: ?>
                            <a class="button button-primary" href="<?= e(url('checkout')) ?>">Proceed to Checkout</a>
                            <a class="button button-secondary" href="<?= e(url('menu')) ?>">Continue Shopping</a>
                        <?php endif; ?>
                    </div>
                </article>
            </aside>
        </div>
    <?php endif; ?>
</section>
