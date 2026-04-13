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
                <button class="dashboard-sidebar__link" type="button" data-dashboard-target="feedback">
                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-sidebar__icon" aria-hidden="true">
                    <span>Feedback</span>
                    <?php if (!empty($recentFeedback)): ?>
                        <span class="dashboard-sidebar__badge"><?= e((string) count($recentFeedback)) ?></span>
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
                        <button class="stat-card stat-card--action" type="button" data-dashboard-target="feedback">
                            <div class="stat-icon"><img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image" aria-hidden="true"></div>
                            <div class="stat-info">
                                <h3><?= e((string) count($recentFeedback ?? [])) ?></h3>
                                <p>Feedback Sent</p>
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
                                    <form action="<?= e(url('dashboard/customer/orders/reorder')) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_id" value="<?= e((string) ($recentOrder['id'] ?? 0)) ?>">
                                        <button type="submit" class="button button-primary">Reorder</button>
                                    </form>
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

                        <article class="content-card dashboard-card">
                            <div class="dashboard-card__header">
                                <h2>Feedback</h2>
                            </div>
                            <?php if (empty($recentFeedback)): ?>
                                <p class="lead">No feedback sent yet.</p>
                                <a href="<?= e(url('feedback')) ?>" class="button button-secondary button-small">Send Feedback</a>
                            <?php else: ?>
                                <?php $latestFeedback = $recentFeedback[0]; ?>
                                <div class="dashboard-summary-list">
                                    <div class="detail-row">
                                        <span class="label">Latest Rating</span>
                                        <span class="value"><?= e((string) ($latestFeedback['rating'] ?? 0)) ?>/5</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Status</span>
                                        <span class="value"><?= e(ucwords(str_replace('_', ' ', (string) ($latestFeedback['status'] ?? 'new')))) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Sent</span>
                                        <span class="value"><?= e(date('M d, Y', strtotime((string) ($latestFeedback['created_at'] ?? 'now')))) ?></span>
                                    </div>
                                </div>
                                <div class="action-row action-row--left">
                                    <button class="button button-secondary button-small" type="button" data-dashboard-target="feedback">View Feedback</button>
                                    <a href="<?= e(url('feedback')) ?>" class="button button-primary button-small">Send More</a>
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
                                    <p>You have not placed any orders yet. Start from the menu and your order history will appear here after checkout.</p>
                                    <a href="<?= e(url('menu')) ?>" class="button button-primary button-small">Browse Menu</a>
                                </div>
                            <?php else: ?>
                                <div class="orders-list">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <?php $status = (string) ($order['status'] ?? 'pending'); ?>
                                        <?php $paymentStatus = (string) ($order['payment_status'] ?? 'pending'); ?>
                                        <?php $canCancelOrder = $status === 'pending' && empty($order['reservation_id']); ?>
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
                                                <form action="<?= e(url('dashboard/customer/orders/reorder')) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? 0)) ?>">
                                                    <button type="submit" class="button button-primary button-small">Reorder</button>
                                                </form>
                                                <button
                                                    class="button button-secondary button-small"
                                                    type="button"
                                                    data-open-order-modal
                                                    data-order-details="<?= e((string) json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                                >View Details</button>
                                                <?php if ($canCancelOrder): ?>
                                                    <form action="<?= e(url('dashboard/customer/orders/cancel')) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? 0)) ?>">
                                                        <button type="submit" class="button button-danger button-small">Cancel Order</button>
                                                    </form>
                                                <?php endif; ?>
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
                                    <p>You have not created any reservations yet. Book a table when you want to plan a visit or attach an order to a reservation.</p>
                                    <a href="<?= e(url('reserve')) ?>" class="button button-primary button-small">Make Reservation</a>
                                </div>
                            <?php else: ?>
                                <div class="reservations-list">
                                    <?php foreach ($recentReservations as $reservation): ?>
                                        <?php $reservationStatus = (string) ($reservation['status'] ?? 'pending'); ?>
                                        <?php
                                            $linkedOrders = is_array($reservation['orders'] ?? null) ? $reservation['orders'] : [];
                                            $hasInProgressOrder = false;

                                            foreach ($linkedOrders as $linkedOrder) {
                                                if (($linkedOrder['status'] ?? 'pending') !== 'pending') {
                                                    $hasInProgressOrder = true;
                                                    break;
                                                }
                                            }

                                            $canCancelReservation = $reservationStatus === 'pending' && !$hasInProgressOrder;
                                        ?>
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
                                                <?php if ($canCancelReservation): ?>
                                                    <form action="<?= e(url('dashboard/customer/reservations/cancel')) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="reservation_id" value="<?= e((string) ($reservation['id'] ?? 0)) ?>">
                                                        <button type="submit" class="button button-danger button-small">Cancel Reservation</button>
                                                    </form>
                                                <?php endif; ?>
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
                            <p class="lead">These details are used for checkout receipts, reservation follow-ups, and staff contact when an order needs attention.</p>
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
                                    <p class="field-hint">Use the name staff should see on orders and reservations.</p>
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
                                    <p class="field-hint">Changing your email updates your next login and dashboard contact address.</p>
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
                                    <p class="field-hint">Use an 11-digit mobile number that starts with 09.</p>
                                </div>
                                <div class="form-field full-width">
                                    <button type="submit" class="button button-primary">Save Profile</button>
                                </div>
                            </form>
                        </article>

                        <article class="records-panel">
                            <div class="records-panel-header">
                                <h3>Password Access</h3>
                            </div>
                            <p class="lead">Change your password here when you know the current one, or use reset access if you need an email link.</p>
                            <form action="<?= e(url('dashboard/customer/password')) ?>" method="post" class="form-grid">
                                <?= csrf_field() ?>
                                <div class="form-field">
                                    <label for="customer_current_password">Current Password</label>
                                    <input
                                        type="password"
                                        id="customer_current_password"
                                        name="current_password"
                                        autocomplete="current-password"
                                        required
                                    >
                                </div>
                                <div class="form-field">
                                    <label for="customer_new_password">New Password</label>
                                    <input
                                        type="password"
                                        id="customer_new_password"
                                        name="password"
                                        autocomplete="new-password"
                                        minlength="8"
                                        required
                                    >
                                    <p class="field-hint">Use at least 8 characters with uppercase, lowercase, and a number.</p>
                                </div>
                                <div class="form-field">
                                    <label for="customer_confirm_password">Confirm New Password</label>
                                    <input
                                        type="password"
                                        id="customer_confirm_password"
                                        name="confirm_password"
                                        autocomplete="new-password"
                                        minlength="8"
                                        required
                                    >
                                </div>
                                <div class="form-field full-width action-row action-row--left">
                                    <button type="submit" class="button button-primary">Update Password</button>
                                    <a href="<?= e(url('password/forgot')) ?>" class="button button-secondary button-small">Reset Password</a>
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

                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Account Tips</h3>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Email</span>
                                    <span class="value">Use an address you can open for reset links.</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone</span>
                                    <span class="value">Keep it reachable for order and reservation updates.</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Password</span>
                                    <span class="value">Reset links expire after 30 minutes.</span>
                                </div>
                            </div>
                        </article>

                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Profile Checklist</h3>
                            </div>
                            <div class="dashboard-summary-list">
                                <div class="detail-row">
                                    <span class="label">Name</span>
                                    <span class="value"><?= $fullName !== '' ? 'Ready' : 'Needs update' ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Email</span>
                                    <span class="value"><?= !empty($user['email']) ? 'Ready' : 'Needs update' ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone</span>
                                    <span class="value"><?= !empty($user['phone']) ? 'Ready' : 'Needs update' ?></span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel" data-dashboard-panel="feedback">
                <div class="records-overview">
                    <div class="records-hero">
                        <div class="records-hero-copy">
                            <span class="records-kicker">Feedback</span>
                            <h3>Track the comments you have sent to the Grande team.</h3>
                        </div>
                        <div class="records-hero-stats">
                            <div class="records-mini-stat">
                                <span class="records-mini-label">Sent</span>
                                <span class="records-mini-value"><?= e((string) count($recentFeedback ?? [])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="records-layout">
                    <div class="records-main-column">
                        <article class="records-panel">
                            <div class="records-panel-header">
                                <h3>Recent Feedback</h3>
                                <a href="<?= e(url('feedback')) ?>" class="button button-primary button-small">Send Feedback</a>
                            </div>
                            <?php if (empty($recentFeedback)): ?>
                                <div class="empty-state-small">
                                    <p>You have not sent feedback yet. Send a comment after an order, reservation, or visit so staff can review it from the dashboard.</p>
                                    <a href="<?= e(url('feedback')) ?>" class="button button-primary button-small">Send Feedback</a>
                                </div>
                            <?php else: ?>
                                <div class="reservations-list">
                                    <?php foreach ($recentFeedback as $feedbackItem): ?>
                                        <?php $feedbackStatus = (string) ($feedbackItem['status'] ?? 'new'); ?>
                                        <article class="reservation-card">
                                            <div class="reservation-header">
                                                <div class="reservation-date">
                                                    <span class="date-label"><?= e(date('M d, Y', strtotime((string) ($feedbackItem['created_at'] ?? 'now')))) ?></span>
                                                    <span class="date-value"><?= e((string) ($feedbackItem['rating'] ?? 0)) ?>/5</span>
                                                </div>
                                                <span class="status-pill status-pill--<?= e($feedbackStatus) ?>"><?= e(ucwords(str_replace('_', ' ', $feedbackStatus))) ?></span>
                                            </div>
                                            <div class="reservation-details">
                                                <div class="detail">
                                                    <img src="<?= e(url('public/icons/chat-bubble.png')) ?>" alt="" class="dashboard-icon-image dashboard-icon-image--detail" aria-hidden="true">
                                                    <span><?= e(ucwords(str_replace('-', ' ', (string) ($feedbackItem['category'] ?? 'feedback')))) ?></span>
                                                </div>
                                            </div>
                                            <p class="lead"><?= e((string) ($feedbackItem['message'] ?? '')) ?></p>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>

                    <div class="records-side-column">
                        <article class="records-panel records-summary-panel">
                            <div class="records-panel-header">
                                <h3>Need Help?</h3>
                            </div>
                            <p class="lead">Send comments about service, food, reservations, or online ordering from the feedback form.</p>
                            <a href="<?= e(url('feedback')) ?>" class="button button-secondary button-small">Open Feedback Form</a>
                        </article>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../partials/dashboard-order-modal.php'; ?>
<?php require __DIR__ . '/../../partials/dashboard-reservation-modal.php'; ?>
