<?php
$adminPriorityPayments = array_slice($pendingPaymentOrders ?? [], 0, 2);
$adminPriorityReservations = array_slice(array_values(array_filter($manageableReservations ?? [], static function (array $reservation): bool {
    return ($reservation['status'] ?? '') === 'pending';
})), 0, 2);
$adminPriorityFeedback = array_slice(array_values(array_filter($manageableFeedback ?? [], static function (array $feedback): bool {
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
                <div class="dashboard-sidebar__avatar"><?= e(strtoupper(substr((string) ($user['first_name'] ?? 'A'), 0, 1))) ?></div>
                <div>
                    <h3><?= e(trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')))) ?></h3>
                    <p>Administrator</p>
                </div>
            </div>

            <nav class="dashboard-sidebar__nav" aria-label="Admin dashboard sections">
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
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="menu">
                    <img src="<?= e(url('public/icons/menu.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Menu</span>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="users">
                    <img src="<?= e(url('public/icons/handshake.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Users</span>
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
                            <span class="dashboard-kicker">Admin Dashboard</span>
                            <h1>Run payments, orders, catalog updates, and staff controls from one workspace.</h1>
                            <div class="welcome-meta">
                                <span class="welcome-meta-item"><?= e((string) ($paymentStats['pending_review'] ?? 0)) ?> payment review item(s)</span>
                                <span class="welcome-meta-item"><?= e((string) ($feedbackStats['new_feedback'] ?? 0)) ?> new feedback item(s)</span>
                                <span class="welcome-meta-item"><?= e((string) ($userManagementStats['active_count'] ?? 0)) ?> active account(s)</span>
                            </div>
                        </div>
                        <div class="welcome-image">
                            <img src="<?= e(url('public/icons/barista.png')) ?>" alt="" class="dashboard-hero-icon" aria-hidden="true">
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
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="menu">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/menu.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($reportOverview['live_menu_items'] ?? 0)) ?></h3>
                                <p>Live Menu Items</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="users">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/handshake.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($userManagementStats['employee_count'] ?? 0)) ?></h3>
                                <p>Employees</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="feedback">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($feedbackStats['new_feedback'] ?? 0)) ?></h3>
                                <p>New Feedback</p>
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
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="menu">
                                    <img src="<?= e(url('public/icons/menu.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Maintain Menu</span>
                                </button>
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="users">
                                    <img src="<?= e(url('public/icons/handshake.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Manage Users</span>
                                </button>
                                <button class="quick-action-link quick-action-link--button" type="button" data-dashboard-target="feedback">
                                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Review Feedback</span>
                                </button>
                            </div>
                        </article>

                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Needs Attention</h2>
                            </div>
                            <div class="activity-list">
                                <?php foreach ($adminPriorityPayments as $queueOrder): ?>
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
                                <?php foreach ($adminPriorityReservations as $queueReservation): ?>
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
                                <?php foreach ($adminPriorityFeedback as $queueFeedback): ?>
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
                                <?php if ($adminPriorityPayments === [] && $adminPriorityReservations === [] && $adminPriorityFeedback === []): ?>
                                    <p class="lead">No urgent payment, reservation, or feedback items are waiting.</p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>

                    <div class="dashboard-side-column">
                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Operations Summary</h2>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Verified Revenue</span>
                                    <span class="value">PHP <?= e(number_format((float) ($reportOverview['verified_revenue'] ?? 0), 2)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Completed Orders</span>
                                    <span class="value"><?= e((string) ($fulfillmentStats['completed_orders'] ?? 0)) ?></span>
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
                $dashboardEyebrow = 'Admin';
                $dashboardTitle = 'Review uploaded payments.';
                $dashboardLead = 'Review uploaded GCash receipts, approve valid payments, or reject invalid ones before deeper order management is added.';
                $reviewActionPath = '/dashboard/admin/payments';
                require __DIR__ . '/../../partials/payment-review-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="orders">
                <?php
                $orderActionPath = '/dashboard/admin/orders';
                require __DIR__ . '/../../partials/order-management-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="reservations">
                <?php
                $reservationActionPath = '/dashboard/admin/reservations';
                require __DIR__ . '/../../partials/reservation-management-dashboard.php';
                ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="menu">
                <?php require __DIR__ . '/../../partials/menu-management-dashboard.php'; ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="users">
                <?php require __DIR__ . '/../../partials/user-management-dashboard.php'; ?>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="feedback">
                <?php
                $feedbackActionPath = '/dashboard/admin/feedback';
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
