<section class="page-section container">
    <p class="eyebrow">Checkout</p>
    <h1>Complete Your Order</h1>
    <p class="lead">Finish a normal order from your cart for to-go or dine-in.</p>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card">
            <h2>Order Details</h2>
            <form id="checkout-form" class="stack-form" method="post" action="<?= e(url('checkout')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="first_name">First Name</label>
                        <input id="first_name" name="first_name" type="text" class="<?= has_error('first_name') ? 'is-invalid' : '' ?>" value="<?= e((string) old('first_name', $user['first_name'] ?? '')) ?>">
                        <?php if ($message = field_error('first_name')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="last_name">Last Name</label>
                        <input id="last_name" name="last_name" type="text" class="<?= has_error('last_name') ? 'is-invalid' : '' ?>" value="<?= e((string) old('last_name', $user['last_name'] ?? '')) ?>">
                        <?php if ($message = field_error('last_name')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" class="<?= has_error('email') ? 'is-invalid' : '' ?>" value="<?= e((string) old('email', $user['email'] ?? '')) ?>">
                        <?php if ($message = field_error('email')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="tel" class="<?= has_error('phone') ? 'is-invalid' : '' ?>" value="<?= e((string) old('phone', $user['phone'] ?? '')) ?>">
                        <?php if ($message = field_error('phone')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                </div>

                <div class="checkout-type-grid">
                    <label class="checkout-type-card">
                        <input type="radio" name="order_type" value="togo" data-order-type-toggle <?= old('order_type', 'togo') === 'togo' ? 'checked' : '' ?>>
                        <span>To-go</span>
                    </label>
                    <label class="checkout-type-card">
                        <input type="radio" name="order_type" value="dinein" data-order-type-toggle <?= old('order_type') === 'dinein' ? 'checked' : '' ?>>
                        <span>Dine-in</span>
                    </label>
                </div>
                <?php if ($message = field_error('order_type')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>

                <div class="form-grid checkout-type-fields">
                    <div class="form-field" data-order-type-section="togo">
                        <label for="ready_time">Ready Time</label>
                        <input id="ready_time" name="ready_time" type="time" class="<?= has_error('ready_time') ? 'is-invalid' : '' ?>" value="<?= e((string) old('ready_time', '')) ?>">
                        <?php if ($message = field_error('ready_time')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                    <div class="form-field" data-order-type-section="dinein">
                        <label for="guest_count">Number of Guests</label>
                        <input id="guest_count" name="guest_count" type="number" min="1" max="20" class="<?= has_error('guest_count') ? 'is-invalid' : '' ?>" value="<?= e((string) old('guest_count', '')) ?>">
                        <?php if ($message = field_error('guest_count')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>
                    </div>
                </div>

                <div class="content-card checkout-payment-card">
                    <h3>Payment Method</h3>
                    <div class="checkout-type-grid">
                        <label class="checkout-type-card">
                            <input type="radio" name="payment_method" value="gcash" <?= old('payment_method', 'gcash') === 'gcash' ? 'checked' : '' ?>>
                            <span>GCash</span>
                        </label>
                    </div>
                    <?php if ($message = field_error('payment_method')): ?><small class="field-error"><?= e((string) $message) ?></small><?php endif; ?>

                    <div class="payment-grid">
                        <div class="payment-qr-card">
                            <h4>Scan to Pay</h4>
                            <div class="payment-qr-frame">
                                <img src="<?= e(url('public/images/gcash-qr-code.png')) ?>" alt="GCash QR Code" class="payment-qr-image">
                            </div>
                            <p class="payment-total-label">Total: <strong>PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></strong></p>
                            <p class="field-note">Open GCash, scan the QR code, complete the payment, then upload the receipt below.</p>
                        </div>

                        <div class="payment-upload-card">
                            <h4>Upload Receipt</h4>
                            <div class="receipt-upload-area">
                                <input id="receipt_image" name="receipt_image" type="file" accept="image/jpeg,image/png,image/webp" class="file-input" data-receipt-input>
                                <label for="receipt_image" class="upload-label">Tap to upload payment receipt</label>
                                <div class="preview-container" data-receipt-preview hidden>
                                    <img src="" alt="Receipt Preview" data-receipt-preview-image>
                                    <button type="button" class="remove-receipt" data-receipt-remove>×</button>
                                </div>
                            </div>
                            <?php if ($message = flash('error')): ?>
                                <?php if (str_contains((string) $message, 'Receipt')): ?>
                                    <small class="field-error"><?= e((string) $message) ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="field-note">Accepted: JPG, PNG, WEBP. Maximum file size is 5MB.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="button button-primary">Place Order</button>
            </form>
        </div>

        <aside class="stack-sidebar">
            <article class="content-card">
                <h3>Order Summary</h3>
                <div class="cart-summary-list">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-summary-item">
                            <div>
                                <strong><?= e($item['item_name']) ?></strong>
                                <p><?= e($item['size']) ?> • Qty <?= e((string) $item['quantity']) ?></p>
                            </div>
                            <span>PHP <?= e(number_format((float) $item['item_price'] * (int) $item['quantity'], 2)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="cart-summary-total">Total: PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
                <a class="button button-secondary" href="<?= e(url('cart')) ?>">Back to Cart</a>
            </article>
        </aside>
    </div>
</section>
