<?php
/** @var array $user */
/** @var array $paymentStats */
/** @var array $pendingPaymentOrders */
/** @var array $reviewedPaymentOrders */
/** @var string $dashboardEyebrow */
/** @var string $dashboardTitle */
/** @var string $dashboardLead */
/** @var string $reviewActionPath */
?>
<section class="page-section container">
    <p class="eyebrow"><?= e($dashboardEyebrow ?? 'Staff') ?></p>
    <h1><?= e($dashboardTitle ?? 'Payment Review') ?></h1>
    <p class="lead"><?= e($dashboardLead ?? '') ?></p>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">Awaiting Review</p>
            <h2><?= e((string) ($paymentStats['pending_review'] ?? 0)) ?></h2>
            <p>Orders with uploaded receipts that still need a staff decision.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Verified</p>
            <h2><?= e((string) ($paymentStats['verified_payments'] ?? 0)) ?></h2>
            <p>Payments already approved from the submitted checkout receipts.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Reservation Orders</p>
            <h2><?= e((string) ($paymentStats['reservation_orders'] ?? 0)) ?></h2>
            <p>Orders linked to reservations that may also affect reservation status.</p>
        </article>
    </div>

    <div class="payment-review-section">
        <div class="payment-review-header">
            <div>
                <p class="eyebrow">Queue</p>
                <h2>Pending Receipt Reviews</h2>
            </div>
            <p class="payment-review-note">Rejecting a reservation-linked payment cancels the linked reservation as well.</p>
        </div>

        <?php if ($pendingPaymentOrders === []): ?>
            <div class="content-card payment-review-empty">
                <h3>No pending receipts.</h3>
                <p>New uploaded checkout receipts will appear here for review.</p>
            </div>
        <?php else: ?>
            <div class="payment-review-list">
                <?php foreach ($pendingPaymentOrders as $order): ?>
                    <?php
                    $customerName = trim(((string) ($order['first_name'] ?? '')) . ' ' . ((string) ($order['last_name'] ?? '')));
                    $customerName = $customerName !== '' ? $customerName : 'Unknown customer';
                    $receiptUrl = !empty($order['receipt_image'])
                        ? url('public/uploads/receipts/' . rawurlencode((string) $order['receipt_image']))
                        : null;
                    ?>
                    <article class="content-card payment-review-card">
                        <div class="payment-review-main">
                            <div class="payment-review-copy">
                                <div class="payment-review-topline">
                                    <p class="eyebrow">Order <?= e((string) ($order['order_number'] ?? '')) ?></p>
                                    <span class="status-pill status-pill--pending">Pending Review</span>
                                </div>
                                <h3><?= e($customerName) ?></h3>
                                <div class="payment-review-meta">
                                    <span><?= e((string) ($order['item_count'] ?? 0)) ?> item(s)</span>
                                    <span>PHP <?= e(number_format((float) ($order['total_amount'] ?? 0), 2)) ?></span>
                                    <span><?= e(date('M d, Y h:i A', strtotime((string) ($order['created_at'] ?? 'now')))) ?></span>
                                </div>
                                <div class="payment-review-support">
                                    <p><strong>Email:</strong> <?= e((string) ($order['email'] ?? '')) ?></p>
                                    <p><strong>Order status:</strong> <?= e(ucfirst((string) ($order['status'] ?? 'pending'))) ?></p>
                                    <?php if (!empty($order['reservation_id'])): ?>
                                        <p>
                                            <strong>Linked reservation:</strong>
                                            <?= e(date('M d, Y', strtotime((string) ($order['reservation_date'] ?? 'now')))) ?>
                                            at
                                            <?= e(date('h:i A', strtotime((string) ($order['reservation_time'] ?? '00:00:00')))) ?>
                                        </p>
                                    <?php else: ?>
                                        <p><strong>Flow:</strong> Direct checkout order</p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($order['items']) && is_array($order['items'])): ?>
                                    <ul class="order-line-list">
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li>
                                                <span><?= e((string) ($item['menu_item_name'] ?? 'Menu item')) ?> x<?= e((string) ($item['quantity'] ?? 0)) ?></span>
                                                <span><?= e((string) ($item['size'] ?? 'Default')) ?>, PHP <?= e(number_format((float) ($item['subtotal'] ?? 0), 2)) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>

                            <?php if ($receiptUrl !== null): ?>
                                <a class="payment-review-receipt" href="<?= e($receiptUrl) ?>" target="_blank" rel="noreferrer">
                                    <img src="<?= e($receiptUrl) ?>" alt="Receipt for order <?= e((string) ($order['order_number'] ?? '')) ?>">
                                    <span>Open Receipt</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="payment-review-actions">
                            <button
                                class="button button-secondary button-small"
                                type="button"
                                data-open-order-modal
                                data-order-details="<?= e((string) json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                            >View Snapshot</button>
                            <form method="post" action="<?= e(url($reviewActionPath)) ?>" data-dashboard-form="payments">
                                <?= csrf_field() ?>
                                <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? 0)) ?>">
                                <input type="hidden" name="payment_action" value="verify">
                                <input type="hidden" name="section" value="payments">
                                <button type="submit" class="button button-primary button-small">Verify Payment</button>
                            </form>
                            <form method="post" action="<?= e(url($reviewActionPath)) ?>" data-dashboard-form="payments">
                                <?= csrf_field() ?>
                                <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? 0)) ?>">
                                <input type="hidden" name="payment_action" value="reject">
                                <input type="hidden" name="section" value="payments">
                                <button type="submit" class="button button-danger button-small">Reject Payment</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="payment-review-section">
        <div class="payment-review-header">
            <div>
                <p class="eyebrow">Recent Decisions</p>
                <h2>Reviewed Payments</h2>
            </div>
        </div>

        <?php if ($reviewedPaymentOrders === []): ?>
            <div class="content-card payment-review-empty">
                <h3>No reviewed payments yet.</h3>
                <p>Approved and rejected receipts will start building the review history here.</p>
            </div>
        <?php else: ?>
            <div class="payment-review-list">
                <?php foreach ($reviewedPaymentOrders as $order): ?>
                    <?php
                    $customerName = trim(((string) ($order['first_name'] ?? '')) . ' ' . ((string) ($order['last_name'] ?? '')));
                    $customerName = $customerName !== '' ? $customerName : 'Unknown customer';
                    $paymentStatus = (string) ($order['payment_status'] ?? 'pending');
                    $receiptUrl = !empty($order['receipt_image'])
                        ? url('public/uploads/receipts/' . rawurlencode((string) $order['receipt_image']))
                        : null;
                    ?>
                    <article class="content-card payment-review-card payment-review-card--compact">
                        <div class="payment-review-main">
                            <div class="payment-review-copy">
                                <div class="payment-review-topline">
                                    <p class="eyebrow">Order <?= e((string) ($order['order_number'] ?? '')) ?></p>
                                    <span class="status-pill status-pill--<?= e($paymentStatus) ?>"><?= e(ucfirst($paymentStatus)) ?></span>
                                </div>
                                <h3><?= e($customerName) ?></h3>
                                <div class="payment-review-meta">
                                    <span>PHP <?= e(number_format((float) ($order['total_amount'] ?? 0), 2)) ?></span>
                                    <span><?= e(ucfirst((string) ($order['status'] ?? 'pending'))) ?></span>
                                    <span><?= e(date('M d, Y h:i A', strtotime((string) ($order['created_at'] ?? 'now')))) ?></span>
                                </div>
                                <?php if (!empty($order['items']) && is_array($order['items'])): ?>
                                    <ul class="order-line-list order-line-list--compact">
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li>
                                                <span><?= e((string) ($item['menu_item_name'] ?? 'Menu item')) ?> x<?= e((string) ($item['quantity'] ?? 0)) ?></span>
                                                <span><?= e((string) ($item['size'] ?? 'Default')) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="reservation-actions">
                                    <button
                                        class="button button-secondary button-small"
                                        type="button"
                                        data-open-order-modal
                                        data-order-details="<?= e((string) json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                    >View Snapshot</button>
                                </div>
                            </div>

                            <?php if ($receiptUrl !== null): ?>
                                <a class="payment-review-receipt payment-review-receipt--small" href="<?= e($receiptUrl) ?>" target="_blank" rel="noreferrer">
                                    <img src="<?= e($receiptUrl) ?>" alt="Receipt for order <?= e((string) ($order['order_number'] ?? '')) ?>">
                                    <span>Receipt</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
