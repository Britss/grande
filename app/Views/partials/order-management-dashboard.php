<?php
/** @var array $fulfillmentStats */
/** @var array $fulfillmentOrders */
/** @var string $orderActionPath */
?>
<section class="page-section container">
<div class="payment-review-section">
    <div class="payment-review-header">
        <div>
            <p class="eyebrow">Fulfillment</p>
            <h2>Manage verified orders</h2>
        </div>
        <p class="payment-review-note">Only verified orders should move into preparation, ready, or completed states.</p>
    </div>

    <div class="dashboard-filter-bar" data-dashboard-filter="orders">
        <div class="dashboard-filter-field dashboard-filter-field--wide">
            <label for="order-filter-search">Search orders</label>
            <input id="order-filter-search" type="search" class="form-control" placeholder="Order number, customer, email" data-filter-search>
        </div>
        <div class="dashboard-filter-field">
            <label for="order-filter-status">Status</label>
            <select id="order-filter-status" class="form-control" data-filter-status>
                <option value="all">All statuses</option>
                <?php foreach (['pending', 'preparing', 'ready', 'completed', 'cancelled'] as $statusOption): ?>
                    <option value="<?= e($statusOption) ?>"><?= e(ucfirst($statusOption)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="dashboard-filter-field">
            <label for="order-filter-flow">Flow</label>
            <select id="order-filter-flow" class="form-control" data-filter-flow>
                <option value="all">All orders</option>
                <option value="direct">Direct checkout</option>
                <option value="reservation">Reservation-linked</option>
            </select>
        </div>
        <button type="button" class="button button-secondary button-small" data-filter-reset>Reset</button>
    </div>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">Active Queue</p>
            <h2><?= e((string) ($fulfillmentStats['active_fulfillment'] ?? 0)) ?></h2>
            <p>Verified orders still being worked on by the store.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Ready Orders</p>
            <h2><?= e((string) ($fulfillmentStats['ready_orders'] ?? 0)) ?></h2>
            <p>Orders already marked ready for pickup, handoff, or table service.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Completed</p>
            <h2><?= e((string) ($fulfillmentStats['completed_orders'] ?? 0)) ?></h2>
            <p>Orders that have already cleared the final fulfillment step.</p>
        </article>
    </div>

    <?php if ($fulfillmentOrders === []): ?>
        <div class="content-card payment-review-empty">
            <h3>No verified orders to manage yet.</h3>
            <p>Orders will appear here after payment is verified from the queue above.</p>
        </div>
    <?php else: ?>
        <div class="payment-review-list">
            <?php foreach ($fulfillmentOrders as $order): ?>
                <?php
                $customerName = trim(((string) ($order['first_name'] ?? '')) . ' ' . ((string) ($order['last_name'] ?? '')));
                $customerName = $customerName !== '' ? $customerName : 'Unknown customer';
                $currentStatus = (string) ($order['status'] ?? 'pending');
                ?>
                <article
                    class="content-card payment-review-card payment-review-card--compact"
                    data-filter-item
                    data-filter-status="<?= e($currentStatus) ?>"
                    data-filter-flow="<?= !empty($order['reservation_id']) ? 'reservation' : 'direct' ?>"
                    data-filter-text="<?= e(strtolower(trim(($order['order_number'] ?? '') . ' ' . $customerName . ' ' . ($order['email'] ?? '')))) ?>"
                >
                    <div class="payment-review-main">
                        <div class="payment-review-copy">
                            <div class="payment-review-topline">
                                <p class="eyebrow">Order <?= e((string) ($order['order_number'] ?? '')) ?></p>
                                <span class="status-pill status-pill--<?= e($currentStatus) ?>"><?= e(ucfirst($currentStatus)) ?></span>
                            </div>
                            <h3><?= e($customerName) ?></h3>
                            <div class="payment-review-meta">
                                <span><?= e((string) ($order['item_count'] ?? 0)) ?> item(s)</span>
                                <span>PHP <?= e(number_format((float) ($order['total_amount'] ?? 0), 2)) ?></span>
                                <span><?= e(date('M d, Y h:i A', strtotime((string) ($order['created_at'] ?? 'now')))) ?></span>
                            </div>
                            <div class="payment-review-support">
                                <p><strong>Email:</strong> <?= e((string) ($order['email'] ?? '')) ?></p>
                                <p><strong>Payment:</strong> <?= e(ucfirst((string) ($order['payment_status'] ?? 'pending'))) ?></p>
                                <?php if (!empty($order['reservation_id'])): ?>
                                    <p>
                                        <strong>Reservation:</strong>
                                        <?= e(date('M d, Y', strtotime((string) ($order['reservation_date'] ?? 'now')))) ?>
                                        at
                                        <?= e(date('h:i A', strtotime((string) ($order['reservation_time'] ?? '00:00:00')))) ?>
                                    </p>
                                <?php else: ?>
                                    <p><strong>Flow:</strong> Direct order checkout</p>
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
                            <div class="reservation-actions">
                                <button
                                    class="button button-secondary button-small"
                                    type="button"
                                    data-open-order-modal
                                    data-order-details="<?= e((string) json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                >View Snapshot</button>
                            </div>
                        </div>

                        <form method="post" action="<?= e(url($orderActionPath)) ?>" class="order-management-form" data-dashboard-form="orders">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? 0)) ?>">
                            <input type="hidden" name="section" value="orders">
                            <label class="field-label" for="order-status-<?= e((string) ($order['id'] ?? 0)) ?>">Order status</label>
                            <select
                                id="order-status-<?= e((string) ($order['id'] ?? 0)) ?>"
                                name="status"
                                class="form-control"
                                <?= in_array($currentStatus, ['completed', 'cancelled', 'rejected'], true) ? 'disabled' : '' ?>
                            >
                                <?php foreach (['pending', 'preparing', 'ready', 'completed', 'cancelled'] as $statusOption): ?>
                                    <option value="<?= e($statusOption) ?>" <?= $currentStatus === $statusOption ? 'selected' : '' ?>>
                                        <?= e(ucfirst($statusOption)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (in_array($currentStatus, ['completed', 'cancelled', 'rejected'], true)): ?>
                                <p class="field-hint">This order is already finalized.</p>
                            <?php else: ?>
                                <button type="submit" class="button button-primary button-small">Update Order</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="content-card payment-review-empty" data-filter-empty hidden>
            <h3>No matching orders.</h3>
            <p>Adjust the status, flow, or search term to widen the queue.</p>
        </div>
    <?php endif; ?>
</div>
</section>
