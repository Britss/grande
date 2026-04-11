<?php
$firstName = (string) ($user['first_name'] ?? 'Customer');
$lastName = (string) ($user['last_name'] ?? '');
$fullName = trim($firstName . ' ' . $lastName);
$greetingHour = (int) date('G');
$greeting = $greetingHour < 12 ? 'Good Morning' : ($greetingHour < 18 ? 'Good Afternoon' : 'Good Evening');
$memberSince = isset($user['created_at']) ? date('F Y', strtotime((string) $user['created_at'])) : 'this year';
$recentOrder = $recentOrders[0] ?? null;
?>
<section class="dashboard-workspace dashboard-workspace--customer">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div class="dashboard-sidebar__brand">
                <img src="<?= e(url('public/images/grandegologo.png')) ?>" alt="Grande. Pandesal + Coffee" class="dashboard-sidebar__logo">
            </div>

            <div class="dashboard-sidebar__profile">
                <div class="dashboard-sidebar__avatar"><?= e(strtoupper(substr($firstName, 0, 1))) ?></div>
                <div>
                    <h3><?= e($fullName !== '' ? $fullName : 'Customer') ?></h3>
                    <p>Member since <?= e($memberSince) ?></p>
                </div>
            </div>

            <nav class="dashboard-sidebar__nav" aria-label="Customer dashboard sections">
                <button class="dashboard-sidebar__link is-active" type="button" data-dashboard-target="overview">
                    <img src="<?= e(url('public/icons/coffee.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Dashboard</span>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="profile">
                    <img src="<?= e(url('public/icons/info.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Profile</span>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="reservations">
                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Reservations</span>
                    <?php if ((int) ($reservationStats['pending_reservations'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($reservationStats['pending_reservations'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="orders">
                    <img src="<?= e(url('public/icons/shopping-cart.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Orders</span>
                    <?php if ((int) ($orderStats['active_orders'] ?? 0) > 0): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) ($orderStats['active_orders'] ?? 0)) ?></span>
                    <?php endif; ?>
                </button>
            </nav>

            <div class="dashboard-sidebar__footer">
                <a href="<?= e(url('feedback')) ?>" class="dashboard-sidebar__support">
                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Send Feedback</span>
                </a>
            </div>
        </aside>

        <div class="dashboard-main">
            <?php if ($status = flash('status')): ?>
                <div class="alert alert-success"><?= e((string) $status) ?></div>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <section class="dashboard-panel is-active" data-dashboard-panel="overview">
                <div class="dashboard-overview customer-dashboard-overview">
                    <div class="welcome-banner dashboard-welcome-banner">
                        <div class="welcome-content">
                            <span class="dashboard-kicker">Customer Dashboard</span>
                            <h1><?= e($greeting) ?>, <?= e($firstName) ?>.</h1>
                            <div class="welcome-meta">
                                <span class="welcome-meta-item">Member since <?= e($memberSince) ?></span>
                                <span class="welcome-meta-item"><?= e((string) ($orderStats['active_orders'] ?? 0)) ?> active order<?= ((int) ($orderStats['active_orders'] ?? 0)) === 1 ? '' : 's' ?></span>
                                <span class="welcome-meta-item"><?= e((string) ($reservationStats['pending_reservations'] ?? 0)) ?> pending reservation<?= ((int) ($reservationStats['pending_reservations'] ?? 0)) === 1 ? '' : 's' ?></span>
                            </div>
                        </div>
                        <div class="welcome-image">
                            <img src="<?= e(url('public/icons/coffee.png')) ?>" alt="" class="dashboard-hero-icon" aria-hidden="true">
                        </div>
                    </div>

                    <div class="dashboard-highlights dashboard-highlights--customer">
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="orders">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/shopping-cart.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($orderStats['total_orders'] ?? 0)) ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="reservations">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($reservationStats['total_reservations'] ?? 0)) ?></h3>
                                <p>Total Reservations</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="orders">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/clock.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($orderStats['awaiting_payment_review'] ?? 0)) ?></h3>
                                <p>Awaiting Review</p>
                            </div>
                        </button>
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="orders">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/diamond.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) ($orderStats['completed_orders'] ?? 0)) ?></h3>
                                <p>Completed Orders</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="dashboard-layout dashboard-layout--customer">
                    <div class="dashboard-main-column">
                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Order Again</h2>
                            </div>
                            <?php if (is_array($recentOrder)): ?>
                                <div class="order-again-info">
                                    <div class="order-detail">
                                        <span class="label">Order #<?= e((string) ($recentOrder['order_number'] ?? '')) ?></span>
                                    </div>
                                    <div class="order-detail">
                                        <span class="label"><?= e(date('M d, Y', strtotime((string) ($recentOrder['created_at'] ?? 'now')))) ?></span>
                                    </div>
                                    <div class="order-total">
                                        <span>PHP <?= e(number_format((float) ($recentOrder['total_amount'] ?? 0), 2)) ?></span>
                                    </div>
                                </div>
                                <div class="action-row action-row--left">
                                    <a href="<?= e(url('menu')) ?>" class="button button-primary">Reorder From Menu</a>
                                    <button
                                        class="button button-secondary"
                                        type="button"
                                        data-open-order-modal
                                        data-order-details="<?= e((string) json_encode($recentOrder, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                    >View Latest Order</button>
                                </div>
                            <?php else: ?>
                                <p class="lead">You have no previous orders yet. Browse the rebuilt menu when you are ready to order.</p>
                                <a href="<?= e(url('menu')) ?>" class="button button-primary">Browse Menu</a>
                            <?php endif; ?>
                        </article>

                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Dashboard Summary</h2>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Account Email</span>
                                    <span class="value"><?= e((string) ($user['email'] ?? '')) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone</span>
                                    <span class="value"><?= e((string) ($user['phone'] ?? '')) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Upcoming Reservations</span>
                                    <span class="value"><?= e((string) ($reservationStats['upcoming_reservations'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Orders Awaiting Review</span>
                                    <span class="value"><?= e((string) ($orderStats['awaiting_payment_review'] ?? 0)) ?></span>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="dashboard-side-column">
                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Quick Actions</h2>
                            </div>
                            <div class="quick-actions-list">
                                <a href="<?= e(url('menu')) ?>" class="quick-action-link">
                                    <img src="<?= e(url('public/icons/bread.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Browse Menu</span>
                                </a>
                                <a href="<?= e(url('reserve')) ?>" class="quick-action-link">
                                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Make Reservation</span>
                                </a>
                                <a href="<?= e(url('feedback')) ?>" class="quick-action-link">
                                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--inline" aria-hidden="true">
                                    <span>Send Feedback</span>
                                </a>
                            </div>
                        </article>

                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Upcoming Reservations</h2>
                            </div>
                            <?php if ($recentReservations === []): ?>
                                <p class="lead">No reservations yet.</p>
                            <?php else: ?>
                                <div class="activity-list">
                                    <?php foreach (array_slice($recentReservations, 0, 3) as $reservation): ?>
                                        <?php $reservationStatus = (string) ($reservation['status'] ?? 'pending'); ?>
                                        <button
                                            class="activity-item-compact activity-item-button"
                                            type="button"
                                            data-open-reservation-modal
                                            data-reservation-details="<?= e((string) json_encode($reservation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                        >
                                            <div class="activity-date">
                                                <span class="date"><?= e(date('M d', strtotime((string) ($reservation['date'] ?? 'now')))) ?></span>
                                                <span class="time"><?= e(date('h:i A', strtotime((string) ($reservation['time'] ?? '00:00:00')))) ?></span>
                                            </div>
                                            <div class="activity-info">
                                                <span class="order-number"><?= e((string) ($reservation['guests'] ?? 0)) ?> guest(s)</span>
                                                <span class="order-date">Reservation #<?= e((string) ($reservation['id'] ?? 0)) ?></span>
                                            </div>
                                            <span class="status-pill status-pill--<?= e($reservationStatus) ?>"><?= e(ucfirst($reservationStatus)) ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="orders">
                <div class="records-overview">
                    <div class="records-hero">
                        <div class="records-hero-copy">
                            <span class="records-kicker">Order History</span>
                            <h3>Inspect your direct orders and reservation-linked orders in one place.</h3>
                        </div>
                        <div class="records-hero-stats">
                            <div class="records-mini-stat">
                                <span class="records-mini-label">Total</span>
                                <span class="records-mini-value"><?= e((string) ($orderStats['total_orders'] ?? 0)) ?></span>
                            </div>
                            <div class="records-mini-stat">
                                <span class="records-mini-label">Active</span>
                                <span class="records-mini-value"><?= e((string) ($orderStats['active_orders'] ?? 0)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="records-layout">
                    <div class="records-main-column">
                        <article class="records-panel">
                            <div class="records-panel-header">
                                <h3>All Orders</h3>
                            </div>
                            <?php if ($recentOrders === []): ?>
                                <div class="empty-state-small">
                                    <p>You have not placed any orders yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="orders-list">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <?php $status = (string) ($order['status'] ?? 'pending'); ?>
                                        <?php $paymentStatus = (string) ($order['payment_status'] ?? 'pending'); ?>
                                        <article class="reservation-card">
                                            <div class="reservation-header">
                                                <div class="reservation-date">
                                                    <span class="date-label">Order ID</span>
                                                    <span class="date-value">#<?= e((string) ($order['order_number'] ?? '')) ?></span>
                                                </div>
                                                <div class="customer-history-badges">
                                                    <span class="status-pill status-pill--<?= e($status) ?>"><?= e(ucfirst($status)) ?></span>
                                                    <span class="status-pill status-pill--<?= e($paymentStatus) ?>"><?= e(ucfirst($paymentStatus)) ?></span>
                                                </div>
                                            </div>
                                            <div class="reservation-details">
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/money.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span>PHP <?= e(number_format((float) ($order['total_amount'] ?? 0), 2)) ?></span>
                                                </div>
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= e(date('M d, Y', strtotime((string) ($order['created_at'] ?? 'now')))) ?></span>
                                                </div>
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/menu.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= !empty($order['reservation_id']) ? 'Reservation order' : 'Direct order' ?></span>
                                                </div>
                                            </div>
                                            <div class="reservation-actions">
                                                <button
                                                    class="button button-secondary button-small"
                                                    type="button"
                                                    data-open-order-modal
                                                    data-order-details="<?= e((string) json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                                >View Details</button>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>

                    <div class="records-side-column">
                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Order Summary</h3>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Total Orders</span>
                                    <span class="value"><?= e((string) ($orderStats['total_orders'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Completed</span>
                                    <span class="value"><?= e((string) ($orderStats['completed_orders'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Awaiting Review</span>
                                    <span class="value"><?= e((string) ($orderStats['awaiting_payment_review'] ?? 0)) ?></span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="reservations">
                <div class="records-overview">
                    <div class="records-hero">
                        <div class="records-hero-copy">
                            <span class="records-kicker">Reservation History</span>
                            <h3>Review reservation schedules, statuses, and linked reservation orders.</h3>
                        </div>
                        <div class="records-hero-stats">
                            <div class="records-mini-stat">
                                <span class="records-mini-label">Total</span>
                                <span class="records-mini-value"><?= e((string) ($reservationStats['total_reservations'] ?? 0)) ?></span>
                            </div>
                            <div class="records-mini-stat">
                                <span class="records-mini-label">Upcoming</span>
                                <span class="records-mini-value"><?= e((string) ($reservationStats['upcoming_reservations'] ?? 0)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="records-layout">
                    <div class="records-main-column">
                        <article class="records-panel">
                            <div class="records-panel-header">
                                <h3>All Reservations</h3>
                            </div>
                            <?php if ($recentReservations === []): ?>
                                <div class="empty-state-small">
                                    <p>You have not created any reservations yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="reservations-list">
                                    <?php foreach ($recentReservations as $reservation): ?>
                                        <?php $reservationStatus = (string) ($reservation['status'] ?? 'pending'); ?>
                                        <article class="reservation-card">
                                            <div class="reservation-header">
                                                <div class="reservation-date">
                                                    <span class="date-label">Reservation</span>
                                                    <span class="date-value">#<?= e((string) ($reservation['id'] ?? 0)) ?></span>
                                                </div>
                                                <span class="status-pill status-pill--<?= e($reservationStatus) ?>"><?= e(ucfirst($reservationStatus)) ?></span>
                                            </div>
                                            <div class="reservation-details">
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/timetable.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= e(date('M d, Y', strtotime((string) ($reservation['date'] ?? 'now')))) ?></span>
                                                </div>
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/clock.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= e(date('h:i A', strtotime((string) ($reservation['time'] ?? '00:00:00')))) ?></span>
                                                </div>
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/info.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= e((string) ($reservation['guests'] ?? 0)) ?> guest(s)</span>
                                                </div>
                                            </div>
                                            <div class="reservation-actions">
                                                <button
                                                    class="button button-secondary button-small"
                                                    type="button"
                                                    data-open-reservation-modal
                                                    data-reservation-details="<?= e((string) json_encode($reservation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                                >View Details</button>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>

                    <div class="records-side-column">
                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Reservation Summary</h3>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Total Reservations</span>
                                    <span class="value"><?= e((string) ($reservationStats['total_reservations'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Upcoming</span>
                                    <span class="value"><?= e((string) ($reservationStats['upcoming_reservations'] ?? 0)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Completed</span>
                                    <span class="value"><?= e((string) ($reservationStats['completed_reservations'] ?? 0)) ?></span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="profile">
                <div class="records-overview">
                    <div class="records-hero">
                        <div class="records-hero-copy">
                            <span class="records-kicker">Account Profile</span>
                            <h3>Keep your ordering and reservation contact details current.</h3>
                        </div>
                    </div>
                </div>

                <div class="records-layout">
                    <div class="records-main-column">
                        <article class="records-panel">
                            <div class="records-panel-header">
                                <h3>Profile Details</h3>
                            </div>
                            <form action="<?= e(url('dashboard/customer/profile')) ?>" method="post" class="form-grid">
                                <?= csrf_field() ?>
                                <div class="form-field">
                                    <label for="customer_first_name">First Name</label>
                                    <input
                                        type="text"
                                        id="customer_first_name"
                                        name="first_name"
                                        value="<?= e((string) ($user['first_name'] ?? '')) ?>"
                                        maxlength="50"
                                        required
                                    >
                                </div>
                                <div class="form-field">
                                    <label for="customer_last_name">Last Name</label>
                                    <input
                                        type="text"
                                        id="customer_last_name"
                                        name="last_name"
                                        value="<?= e((string) ($user['last_name'] ?? '')) ?>"
                                        maxlength="50"
                                        required
                                    >
                                </div>
                                <div class="form-field">
                                    <label for="customer_email">Email</label>
                                    <input
                                        type="email"
                                        id="customer_email"
                                        name="email"
                                        value="<?= e((string) ($user['email'] ?? '')) ?>"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                                <div class="form-field">
                                    <label for="customer_phone">Phone</label>
                                    <input
                                        type="tel"
                                        id="customer_phone"
                                        name="phone"
                                        value="<?= e((string) ($user['phone'] ?? '')) ?>"
                                        pattern="09[0-9]{9}"
                                        maxlength="11"
                                        required
                                    >
                                </div>
                                <div class="form-field full-width">
                                    <button type="submit" class="button button-primary">Save Profile</button>
                                </div>
                            </form>
                        </article>
                    </div>

                    <div class="records-side-column">
                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Account Summary</h3>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Name</span>
                                    <span class="value"><?= e($fullName !== '' ? $fullName : 'Customer') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Email</span>
                                    <span class="value"><?= e((string) ($user['email'] ?? '')) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone</span>
                                    <span class="value"><?= e((string) ($user['phone'] ?? '')) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Member Since</span>
                                    <span class="value"><?= e($memberSince) ?></span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../partials/dashboard-order-modal.php'; ?>
<?php require __DIR__ . '/../../partials/dashboard-reservation-modal.php'; ?>
