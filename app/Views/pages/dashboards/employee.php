<?php
$priorityPaymentOrders = array_slice($pendingPaymentOrders ?? [], 0, 2);
$priorityReservations = array_slice(array_values(array_filter($manageableReservations ?? [], static function (array $reservation): bool {
    return ($reservation['status'] ?? '') === 'pending';
})), 0, 2);
$priorityFeedback = array_slice(array_values(array_filter($manageableFeedback ?? [], static function (array $feedback): bool {
    return ($feedback['status'] ?? '') === 'new';
})), 0, 2);
?>
<section class="dashboard-workspace dashboard-workspace--staff">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div class="dashboard-sidebar__brand">
                <img src="<?= e(url('public/images/grandegologo.png')) ?>" alt="Grande. Pandesal + Coffee" class="dashboard-sidebar__logo">
            </div>

            <div class="dashboard-sidebar__profile">
                <div class="dashboard-sidebar__avatar"><?= e(strtoupper(substr((string) ($user['first_name'] ?? 'E'), 0, 1))) ?></div>
                <div>
                    <h3><?= e(trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')))) ?></h3>
                    <p>Operations Staff</p>
                </div>
            </div>

            <nav class="dashboard-sidebar__nav" aria-label="Employee dashboard sections">
                <button class="dashboard-sidebar__link is-active" type="button" data-dashboard-target="overview">
                    <img src="<?= e(url('public/icons/coffee.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Overview</span>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="payments">
                    <img src="<?= e(url('public/icons/money.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Payments</span>
                    <?php if ((int) ($paymentStats['pending_review'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($paymentStats['pending_review'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="orders">
                    <img src="<?= e(url('public/icons/shopping-cart.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Orders</span>
                    <?php if ((int) ($fulfillmentStats['active_fulfillment'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($fulfillmentStats['active_fulfillment'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="reservations">
                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Reservations</span>
                    <?php if ((int) ($reservationManagementStats['pending_reservations'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($reservationManagementStats['pending_reservations'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="feedback">
                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Feedback</span>
                    <?php if ((int) ($feedbackStats['new_feedback'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($feedbackStats['new_feedback'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="reports">
                    <img src="<?= e(url('public/icons/info.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Reports</span>
                </button>
            </nav>
        </aside>

        <div class="dashboard-main">
            <?php if ($status = flash('status')): ?>
                <div class="alert alert-success"><?= e((string) $status) ?></div>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <section class="dashboard-panel is-active" data-dashboard-panel="overview">
                <div class="dashboard-overview">
                    <div class="welcome-banner dashboard-welcome-banner">
                        <div class="welcome-content">
                            <span class="dashboard-kicker">Employee Dashboard</span>
                            <h1>Work the daily queue with payment, fulfillment, reservation, and feedback visibility.</h1>
                            <div class="welcome-meta">
                                <span class="welcome-meta-item"><?= e((string) ($paymentStats['pending_review'] ?? 0)) ?> payment check(s)</span>
                                <span class="welcome-meta-item"><?= e((string) ($feedbackStats['new_feedback'] ?? 0)) ?> new feedback item(s)</span>
                                <span class="welcome-meta-item"><?= e((string) ($reservationManagementStats['pending_reservations'] ?? 0)) ?> pending reservation(s)</span>
                            </div>
                        </div>
                        <div class="welcome-image">
                            <img src="<?= e(url('public/icons/handshake.png')) ?>" alt="" class="dashboard-hero-icon" aria-hidden="true">
                        </div>
                    </div>

                    <div class="dashboard-highlights dashboard-highlights--staff">
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="payments">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/money.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($paymentStats['pending_review'] ?? 0)) ?></h3>
                                <p>Pending Payments</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="orders">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/shopping-cart.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($fulfillmentStats['active_fulfillment'] ?? 0)) ?></h3>
                                <p>Active Orders</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="feedback">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($feedbackStats['in_review_feedback'] ?? 0)) ?></h3>
                                <p>Feedback In Review</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="reports">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/info.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($reportOverview['open_feedback'] ?? 0)) ?></h3>
                                <p>Open Feedback</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="dashboard-layout dashboard-layout--staff">
                    <div class="dashboard-main-column">
                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Quick Actions</h2>
                            </div>
                            <div class="quick-actions-list">
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="payments">
                                    <img src="<?= e(url('public/icons/money.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Review Payments</span>
                                </button>
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="orders">
                                    <img src="<?= e(url('public/icons/shopping-cart1.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Manage Orders</span>
                                </button>
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="reservations">
                                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Manage Reservations</span>
                                </button>
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="feedback">
                                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Review Feedback</span>
                                </button>
                            </div>
                        </article>

                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Priority Queue</h2>
                            </div>
                            <div class="activity-list">
                                <?php foreach ($priorityPaymentOrders as $queueOrder): ?>
                                    <button
                                        class="activity-item-compact activity-item-button"
                                        type="button"
                                        data-dashboard-target="payments"
                                    >
                                        <div class="activity-info">
                                            <span class="order-number">Payment <?= e((string) ($queueOrder['order_number'] ?? '')) ?></span>
                                            <span class="order-date"><?= e(date('M d, h:i A', strtotime((string) ($queueOrder['created_at'] ?? 'now')))) ?></span>
                                        </div>
                                        <span class="status-pill status-pill--pending">Pending</span>
                                    </button>
                                <?php endforeach; ?>
                                <?php foreach ($priorityReservations as $queueReservation): ?>
                                    <button
                                        class="activity-item-compact activity-item-button"
                                        type="button"
                                        data-dashboard-target="reservations"
                                    >
                                        <div class="activity-info">
                                            <span class="order-number">Reservation #<?= e((string) ($queueReservation['id'] ?? 0)) ?></span>
                                            <span class="order-date">
                                                <?= e(date('M d', strtotime((string) ($queueReservation['date'] ?? 'now')))) ?>
                                                at
                                                <?= e(date('h:i A', strtotime((string) ($queueReservation['time'] ?? '00:00:00')))) ?>
                                            </span>
                                        </div>
                                        <span class="status-pill status-pill--pending">Pending</span>
                                    </button>
                                <?php endforeach; ?>
                                <?php foreach ($priorityFeedback as $queueFeedback): ?>
                                    <button
                                        class="activity-item-compact activity-item-button"
                                        type="button"
                                        data-dashboard-target="feedback"
                                    >
                                        <div class="activity-info">
                                            <span class="order-number">Feedback #<?= e((string) ($queueFeedback['id'] ?? 0)) ?></span>
                                            <span class="order-date"><?= e(ucwords(str_replace('-', ' ', (string) ($queueFeedback['category'] ?? 'feedback')))) ?></span>
                                        </div>
                                        <span class="status-pill status-pill--new">New</span>
                                    </button>
                                <?php endforeach; ?>
                                <?php if ($priorityPaymentOrders === [] && $priorityReservations === [] && $priorityFeedback === []): ?>
                                    <p class="lead">No urgent payment, reservation, or feedback items are waiting.</p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>

                    <div class="dashboard-side-column">
                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Queue Summary</h2>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Verified Payments</span>
                                    <span class="value"><?= e((string) ($paymentStats['verified_payments'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Ready Orders</span>
                                    <span class="value"><?= e((string) ($fulfillmentStats['ready_orders'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Confirmed Reservations</span>
                                    <span class="value"><?= e((string) ($reservationManagementStats['confirmed_reservations'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Open Feedback</span>
                                    <span class="value"><?= e((string) ($reportOverview['open_feedback'] ?? 0)) ?></span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="payments">
                <?php
                $dashboardEyebrow = 'Employee';
                $dashboardTitle = 'Work the payment queue.';
                $dashboardLead = 'Employees can review uploaded checkout receipts, approve payments that look valid, and reject problem payments before orders move deeper into the workflow.';
                $reviewActionPath = '/dashboard/employee/payments';
                require __DIR__ . '/../../partials/payment-review-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="orders">
                <?php
                $orderActionPath = '/dashboard/employee/orders';
                require __DIR__ . '/../../partials/order-management-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="reservations">
                <?php
                $reservationActionPath = '/dashboard/employee/reservations';
                require __DIR__ . '/../../partials/reservation-management-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="feedback">
                <?php
                $feedbackActionPath = '/dashboard/employee/feedback';
                require __DIR__ . '/../../partials/feedback-management-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="reports">
                <?php require __DIR__ . '/../../partials/reporting-dashboard.php'; ?>
            </section>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../partials/dashboard-order-modal.php'; ?>
<?php require __DIR__ . '/../../partials/dashboard-reservation-modal.php'; ?>
