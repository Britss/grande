<section class="page-section container">
    <h1>Complete Your Order</h1>
    <p class="lead">Confirm your details, choose how you want your bread and drinks prepared, then upload your GCash receipt.</p>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card checkout-panel">
            <div class="cart-card-heading checkout-card-heading">
                <div>
                    <h2>Order Details</h2>
                    <p class="cart-card-subtitle checkout-section-note">We use these details for order updates, pickup confirmation, and smooth handoff at the counter.</p>
                </div>
            </div>
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
                    <div class="cart-card-heading checkout-card-heading">
                        <div>
                            <h3>Payment Method</h3>
                            <p class="cart-card-subtitle checkout-section-note">GCash payments are reviewed by staff before your bread and drinks move to preparation.</p>
                        </div>
                    </div>
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
                            <p class="payment-total-label">Order total</p>
                            <p class="payment-total-amount">PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
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

                <button type="submit" class="button button-primary checkout-submit-button">Place Order</button>
            </form>
        </div>

        <aside class="stack-sidebar">
            <article class="content-card checkout-summary-card">
                <h3>Order Summary</h3>
                <p class="cart-summary-meta"><?= e((string) $cartTotals['item_count']) ?> item(s) ready for checkout</p>
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
                <div class="cart-summary-breakdown" aria-label="Checkout summary breakdown">
                    <div class="cart-summary-row">
                        <span>Items</span>
                        <strong><?= e((string) $cartTotals['item_count']) ?></strong>
                    </div>
                    <div class="cart-summary-row">
                        <span>Subtotal</span>
                        <strong>PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></strong>
                    </div>
                </div>
                <p class="cart-summary-note">Upload a valid GCash receipt so staff can verify payment and start preparing your order.</p>
                <div class="action-row cart-sidebar-actions checkout-sidebar-actions">
                    <a class="button button-secondary" href="<?= e(url('cart')) ?>">Back to Cart</a>
                </div>
            </article>
        </aside>
    </div>
</section>