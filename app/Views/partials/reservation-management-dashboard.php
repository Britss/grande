<?php
/** @var array $reservationManagementStats */
/** @var array $manageableReservations */
/** @var string $reservationActionPath */
?>
<section class="page-section container">
    <div class="payment-review-section">
        <div class="payment-review-header">
            <div>
                <p class="eyebrow">Reservations</p>
                <h2>Manage reservation lifecycle</h2>
            </div>
            <p class="payment-review-note">Staff can only move a reservation once all linked reservation orders are fully payment-verified.</p>
        </div>

        <div class="dashboard-filter-bar" data-dashboard-filter="reservations">
            <div class="dashboard-filter-field dashboard-filter-field--wide">
                <label for="reservation-filter-search">Search reservations</label>
                <input id="reservation-filter-search" type="search" class="form-control" placeholder="Guest, email, phone, reservation number" data-filter-search>
            </div>
            <div class="dashboard-filter-field">
                <label for="reservation-filter-status">Status</label>
                <select id="reservation-filter-status" class="form-control" data-filter-status>
                    <option value="all">All statuses</option>
                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $statusOption): ?>
                        <option value="<?= e($statusOption) ?>"><?= e(ucfirst($statusOption)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="dashboard-filter-field">
                <label for="reservation-filter-lock">Payment check</label>
                <select id="reservation-filter-lock" class="form-control" data-filter-lock>
                    <option value="all">All records</option>
                    <option value="ready">Ready to update</option>
                    <option value="locked">Needs payment verification</option>
                </select>
            </div>
            <button type="button" class="button button-secondary button-small" data-filter-reset>Reset</button>
        </div>

        <div class="content-grid three-up staff-summary-grid">
            <article class="content-card">
                <p class="eyebrow">Pending</p>
                <h2><?= e((string) ($reservationManagementStats['pending_reservations'] ?? 0)) ?></h2>
                <p>Reservations still waiting to be confirmed or otherwise processed.</p>
            </article>
            <article class="content-card">
                <p class="eyebrow">Confirmed</p>
                <h2><?= e((string) ($reservationManagementStats['confirmed_reservations'] ?? 0)) ?></h2>
                <p>Reservations already approved and expected by the store.</p>
            </article>
            <article class="content-card">
                <p class="eyebrow">Completed</p>
                <h2><?= e((string) ($reservationManagementStats['completed_reservations'] ?? 0)) ?></h2>
                <p>Visits already closed out on the reservation side.</p>
            </article>
        </div>

        <?php if ($manageableReservations === []): ?>
            <div class="content-card payment-review-empty">
                <h3>No reservations to manage yet.</h3>
                <p>Reservation records will appear here after customers finish the reservation flow.</p>
            </div>
        <?php else: ?>
            <div class="payment-review-list">
                <?php foreach ($manageableReservations as $reservation): ?>
                    <?php
                    $customerName = trim(((string) ($reservation['first_name'] ?? '')) . ' ' . ((string) ($reservation['last_name'] ?? '')));
                    $customerName = $customerName !== '' ? $customerName : 'Unknown customer';
                    $currentStatus = (string) ($reservation['status'] ?? 'pending');
                    $orderCount = (int) ($reservation['order_count'] ?? 0);
                    $verifiedOrderCount = (int) ($reservation['verified_order_count'] ?? 0);
                    $isLocked = $orderCount === 0 || $verifiedOrderCount !== $orderCount;
                    ?>
                    <article
                        class="content-card payment-review-card payment-review-card--compact"
                        data-filter-item
                        data-filter-status="<?= e($currentStatus) ?>"
                        data-filter-lock="<?= $isLocked ? 'locked' : 'ready' ?>"
                        data-filter-text="<?= e(strtolower(trim(($reservation['id'] ?? '') . ' ' . $customerName . ' ' . ($reservation['email'] ?? '') . ' ' . ($reservation['phone'] ?? '')))) ?>"
                    >
                        <div class="payment-review-main">
                            <div class="payment-review-copy">
                                <div class="payment-review-topline">
                                    <p class="eyebrow">Reservation #<?= e((string) ($reservation['id'] ?? 0)) ?></p>
                                    <span class="status-pill status-pill--<?= e($currentStatus) ?>"><?= e(ucfirst($currentStatus)) ?></span>
                                </div>
                                <h3><?= e($customerName) ?></h3>
                                <div class="payment-review-meta">
                                    <span><?= e(date('M d, Y', strtotime((string) ($reservation['date'] ?? 'now')))) ?></span>
                                    <span><?= e(date('h:i A', strtotime((string) ($reservation['time'] ?? '00:00:00')))) ?></span>
                                    <span><?= e((string) ($reservation['guests'] ?? 0)) ?> guest(s)</span>
                                </div>
                                <div class="payment-review-support">
                                    <p><strong>Email:</strong> <?= e((string) ($reservation['email'] ?? '')) ?></p>
                                    <p><strong>Phone:</strong> <?= e((string) ($reservation['phone'] ?? '')) ?></p>
                                    <p><strong>Linked orders:</strong> <?= e((string) $verifiedOrderCount) ?> of <?= e((string) $orderCount) ?> verified</p>
                                </div>
                                <div class="reservation-actions">
                                    <button
                                        class="button button-secondary button-small"
                                        type="button"
                                        data-open-reservation-modal
                                        data-reservation-details="<?= e((string) json_encode($reservation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"
                                    >View Snapshot</button>
                                </div>
                            </div>

                            <form method="post" action="<?= e(url($reservationActionPath)) ?>" class="order-management-form" data-dashboard-form="reservations">
                                <?= csrf_field() ?>
                                <input type="hidden" name="reservation_id" value="<?= e((string) ($reservation['id'] ?? 0)) ?>">
                                <input type="hidden" name="section" value="reservations">
                                <label class="field-label" for="reservation-status-<?= e((string) ($reservation['id'] ?? 0)) ?>">Reservation status</label>
                                <select
                                    id="reservation-status-<?= e((string) ($reservation['id'] ?? 0)) ?>"
                                    name="status"
                                    class="form-control"
                                    <?= $isLocked || in_array($currentStatus, ['completed', 'cancelled'], true) ? 'disabled' : '' ?>
                                >
                                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $statusOption): ?>
                                        <option value="<?= e($statusOption) ?>" <?= $currentStatus === $statusOption ? 'selected' : '' ?>>
                                            <?= e(ucfirst($statusOption)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (in_array($currentStatus, ['completed', 'cancelled'], true)): ?>
                                    <p class="field-hint">This reservation is already finalized.</p>
                                <?php elseif ($isLocked): ?>
                                    <p class="field-hint">Verify every linked reservation order payment first before updating this reservation.</p>
                                <?php else: ?>
                                    <button type="submit" class="button button-primary button-small">Update Reservation</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="content-card payment-review-empty" data-filter-empty hidden>
                <h3>No matching reservations.</h3>
                <p>Adjust the status, payment check, or search term to widen the queue.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
