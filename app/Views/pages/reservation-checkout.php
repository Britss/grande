<section class="page-section container">
    <p class="eyebrow">Reservation Checkout</p>
    <h1>Complete Your Reservation Order</h1>
    <p class="lead">Your reservation is saved. Upload your GCash receipt so your selected items can be linked to your visit.</p>

    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card checkout-panel">
            <h2>Reservation Details</h2>
            <p class="checkout-section-note">Staff will use these details to match your table request with your order.</p>
            <div class="cart-summary-list">
                <div class="cart-summary-item">
                    <div>
                        <strong><?= e($reservation['name']) ?></strong>
                        <p><?= e($reservation['email']) ?> • <?= e($reservation['phone']) ?></p>
                    </div>
                </div>
                <div class="cart-summary-item">
                    <div>
                        <strong>Date & Time</strong>
                        <p><?= e($reservation['date']) ?> • <?= e($reservation['time']) ?></p>
                    </div>
                </div>
                <div class="cart-summary-item">
                    <div>
                        <strong>Guests</strong>
                        <p><?= e((string) $reservation['guests']) ?> guest(s)</p>
                    </div>
                </div>
            </div>

            <form id="reservation-checkout-form" class="stack-form" method="post" action="<?= e(url('reservation-checkout')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="payment_method" value="gcash">

                <div class="content-card checkout-payment-card">
                    <h3>Payment Method</h3>
                    <p class="checkout-section-note">GCash payments are reviewed by staff before the reservation order moves to preparation.</p>
                    <div class="checkout-type-grid">
                        <label class="checkout-type-card">
                            <input type="radio" checked disabled>
                            <span>GCash</span>
                        </label>
                    </div>

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
                                <input id="reservation_receipt_image" name="receipt_image" type="file" accept="image/jpeg,image/png,image/webp" class="file-input" data-receipt-input>
                                <label for="reservation_receipt_image" class="upload-label">Tap to upload payment receipt</label>
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

                <button type="submit" class="button button-primary checkout-submit-button">Place Reservation Order</button>
                <a class="button button-secondary" href="<?= e(url('cart?from=reservation')) ?>">Back to Cart</a>
            </form>
        </div>

        <aside class="stack-sidebar">
            <article class="content-card checkout-summary-card">
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
            </article>
        </aside>
    </div>
</section>
