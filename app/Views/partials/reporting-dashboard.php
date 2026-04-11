<?php
/** @var array $reportOverview */
/** @var array $orderStatusBreakdown */
/** @var array $reservationStatusBreakdown */
/** @var array $dailySales */
/** @var array $orderVolumeByDay */
/** @var array $reservationVolumeByDay */
/** @var array $topSellingMenuItems */
/** @var array $reportRange */
/** @var array $rangeSummary */
/** @var array $orderDrilldown */
/** @var array $reservationDrilldown */
/** @var array $recentAuditLogs */
/** @var bool $canViewBusinessReports */
$canViewBusinessReports = (bool) ($canViewBusinessReports ?? false);
$reportActionPath = (($user['role'] ?? '') === 'employee') ? '/dashboard/employee' : '/dashboard/admin';
$reportRange = $reportRange ?? ['start' => date('Y-m-d', strtotime('-6 days')), 'end' => date('Y-m-d'), 'label' => 'Last 7 days'];
$maxDailySales = max(1, ...array_map(static fn (array $row): float => (float) ($row['total'] ?? 0), $dailySales ?? []));
$maxOrderVolume = max(1, ...array_map(static fn (array $row): float => (float) ($row['total'] ?? 0), $orderVolumeByDay ?? []));
$maxReservationVolume = max(1, ...array_map(static fn (array $row): float => (float) ($row['total'] ?? 0), $reservationVolumeByDay ?? []));
$maxTopQuantity = max(1, ...array_map(static fn (array $row): float => (float) ($row['total_quantity'] ?? 0), $topSellingMenuItems ?? []));
?>
<section class="page-section container">
    <div class="payment-review-header">
        <div>
            <p class="eyebrow">Reports & Audit</p>
            <h2>Review store-wide operational signals</h2>
        </div>
        <p class="payment-review-note">This is the groundwork for later live updates: stable panels, stable section keys, and logged staff actions.</p>
    </div>

    <div class="dashboard-filter-bar dashboard-filter-bar--reports">
        <div>
            <p class="eyebrow">Range</p>
            <h3><?= e((string) ($reportRange['label'] ?? 'Selected range')) ?></h3>
        </div>
        <form class="report-range-form" method="get" action="<?= e(url($reportActionPath)) ?>">
            <input type="hidden" name="section" value="reports">
            <label>
                From
                <input type="date" name="report_start" value="<?= e((string) ($reportRange['start'] ?? '')) ?>">
            </label>
            <label>
                To
                <input type="date" name="report_end" value="<?= e((string) ($reportRange['end'] ?? '')) ?>">
            </label>
            <button class="button button-primary button-small" type="submit">Apply</button>
            <button class="button button-secondary button-small" type="button" onclick="window.print()">Print</button>
        </form>
    </div>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">Range Revenue</p>
            <h2>PHP <?= e(number_format((float) ($rangeSummary['verified_revenue'] ?? 0), 2)) ?></h2>
            <p>Verified payments inside the selected report range.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Range Orders</p>
            <h2><?= e((string) ($rangeSummary['total_orders'] ?? 0)) ?></h2>
            <p>Orders created inside the selected report range.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Range Reservations</p>
            <h2><?= e((string) ($rangeSummary['total_reservations'] ?? 0)) ?></h2>
            <p>Reservations scheduled inside the selected report range.</p>
        </article>
    </div>

    <div class="report-chart-grid">
        <?php if ($canViewBusinessReports): ?>
            <article class="content-card report-chart-card">
                <div class="dashboard-card__header">
                    <h3>Daily Sales</h3>
                </div>
                <div class="report-bar-list">
                    <?php foreach (($dailySales ?? []) as $row): ?>
                        <?php $width = ((float) ($row['total'] ?? 0) / $maxDailySales) * 100; ?>
                        <div class="report-bar-row">
                            <span class="report-bar-label"><?= e((string) ($row['label'] ?? 'Day')) ?></span>
                            <span class="report-bar-track">
                                <span class="report-bar-fill report-bar-fill--revenue" style="width: <?= e((string) $width) ?>%"></span>
                            </span>
                            <strong>PHP <?= e(number_format((float) ($row['total'] ?? 0), 0)) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endif; ?>

        <article class="content-card report-chart-card">
            <div class="dashboard-card__header">
                <h3>Order Volume</h3>
            </div>
            <div class="report-bar-list">
                <?php foreach (($orderVolumeByDay ?? []) as $row): ?>
                    <?php $width = ((float) ($row['total'] ?? 0) / $maxOrderVolume) * 100; ?>
                    <div class="report-bar-row">
                        <span class="report-bar-label"><?= e((string) ($row['label'] ?? 'Day')) ?></span>
                        <span class="report-bar-track">
                            <span class="report-bar-fill" style="width: <?= e((string) $width) ?>%"></span>
                        </span>
                        <strong><?= e((string) (int) ($row['total'] ?? 0)) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="content-card report-chart-card">
            <div class="dashboard-card__header">
                <h3>Reservation Volume</h3>
            </div>
            <div class="report-bar-list">
                <?php foreach (($reservationVolumeByDay ?? []) as $row): ?>
                    <?php $width = ((float) ($row['total'] ?? 0) / $maxReservationVolume) * 100; ?>
                    <div class="report-bar-row">
                        <span class="report-bar-label"><?= e((string) ($row['label'] ?? 'Day')) ?></span>
                        <span class="report-bar-track">
                            <span class="report-bar-fill report-bar-fill--reservation" style="width: <?= e((string) $width) ?>%"></span>
                        </span>
                        <strong><?= e((string) (int) ($row['total'] ?? 0)) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </div>

    <div class="management-grid">
        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Current Totals</h3>
            </div>
            <div class="dashboard-summary-list">
                <div class="detail-row">
                    <span class="label">Total Orders</span>
                    <span class="value"><?= e((string) ($reportOverview['total_orders'] ?? 0)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Verified Orders</span>
                    <span class="value"><?= e((string) ($reportOverview['verified_orders'] ?? 0)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Total Reservations</span>
                    <span class="value"><?= e((string) ($reportOverview['total_reservations'] ?? 0)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Live Menu Items</span>
                    <span class="value"><?= e((string) ($reportOverview['live_menu_items'] ?? 0)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Archived Menu Items</span>
                    <span class="value"><?= e((string) ($reportOverview['archived_menu_items'] ?? 0)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Customer Accounts</span>
                    <span class="value"><?= e((string) ($reportOverview['total_customers'] ?? 0)) ?></span>
                </div>
            </div>
        </article>

        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Order Status Breakdown</h3>
            </div>
            <div class="management-stack management-stack--tight">
                <?php foreach ($orderStatusBreakdown as $row): ?>
                    <div class="detail-row">
                        <span class="label"><?= e(ucfirst((string) ($row['status'] ?? 'pending'))) ?></span>
                        <span class="value"><?= e((string) ($row['total'] ?? 0)) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="dashboard-card__header dashboard-card__header--spaced">
                <h3>Reservation Status Breakdown</h3>
            </div>
            <div class="management-stack management-stack--tight">
                <?php foreach ($reservationStatusBreakdown as $row): ?>
                    <div class="detail-row">
                        <span class="label"><?= e(ucfirst((string) ($row['status'] ?? 'pending'))) ?></span>
                        <span class="value"><?= e((string) ($row['total'] ?? 0)) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </div>

    <?php if ($canViewBusinessReports): ?>
        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Top-Selling Menu Items</h3>
            </div>
            <?php if (($topSellingMenuItems ?? []) === []): ?>
                <p class="lead">No verified sales have been recorded yet.</p>
            <?php else: ?>
                <div class="report-selling-list">
                    <?php foreach ($topSellingMenuItems as $index => $item): ?>
                        <?php $width = ((float) ($item['total_quantity'] ?? 0) / $maxTopQuantity) * 100; ?>
                        <article class="report-selling-item">
                            <div>
                                <span class="report-rank">#<?= e((string) ($index + 1)) ?></span>
                                <h4><?= e((string) ($item['name'] ?? 'Menu item')) ?></h4>
                                <p class="inline-note"><?= e((string) ($item['category'] ?? 'Menu')) ?></p>
                            </div>
                            <div class="report-selling-meter">
                                <span class="report-bar-track">
                                    <span class="report-bar-fill report-bar-fill--best" style="width: <?= e((string) $width) ?>%"></span>
                                </span>
                                <p><?= e((string) (int) ($item['total_quantity'] ?? 0)) ?> sold | PHP <?= e(number_format((float) ($item['total_revenue'] ?? 0), 2)) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    <?php endif; ?>

    <div class="management-grid">
        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Order Drilldown</h3>
            </div>
            <p class="inline-note">Use browser print or save as PDF after selecting a date range.</p>
            <?php if (($orderDrilldown ?? []) === []): ?>
                <p class="lead">No orders were created in this range.</p>
            <?php else: ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDrilldown as $order): ?>
                                <tr>
                                    <td><?= e((string) ($order['order_number'] ?? ('#' . ($order['id'] ?? '')))) ?></td>
                                    <td><?= e(date('M d, Y', strtotime((string) ($order['created_at'] ?? 'now')))) ?></td>
                                    <td><?= e(ucfirst((string) ($order['status'] ?? 'pending'))) ?></td>
                                    <td><?= e(ucfirst((string) ($order['payment_status'] ?? 'pending'))) ?></td>
                                    <td>PHP <?= e(number_format((float) ($order['total_amount'] ?? 0), 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>

        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Reservation Drilldown</h3>
            </div>
            <p class="inline-note">This table follows the same selected date range as the report cards.</p>
            <?php if (($reservationDrilldown ?? []) === []): ?>
                <p class="lead">No reservations are scheduled in this range.</p>
            <?php else: ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Guests</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservationDrilldown as $reservation): ?>
                                <?php $guestName = trim(((string) ($reservation['first_name'] ?? '')) . ' ' . ((string) ($reservation['last_name'] ?? ''))); ?>
                                <tr>
                                    <td><?= e($guestName !== '' ? $guestName : ('Reservation #' . ($reservation['id'] ?? ''))) ?></td>
                                    <td><?= e(date('M d, Y', strtotime((string) ($reservation['date'] ?? 'now')))) ?></td>
                                    <td><?= e(date('h:i A', strtotime((string) ($reservation['time'] ?? '00:00')))) ?></td>
                                    <td><?= e((string) ($reservation['guests'] ?? 0)) ?></td>
                                    <td><?= e(ucfirst((string) ($reservation['status'] ?? 'pending'))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <article class="content-card management-card">
        <div class="dashboard-card__header">
            <h3>Recent Audit Trail</h3>
        </div>
        <?php if ($recentAuditLogs === []): ?>
            <p class="lead">No logged staff actions yet.</p>
        <?php else: ?>
            <div class="audit-log-list">
                <?php foreach ($recentAuditLogs as $log): ?>
                    <?php
                    $actorName = trim(((string) ($log['first_name'] ?? '')) . ' ' . ((string) ($log['last_name'] ?? '')));
                    $actorName = $actorName !== '' ? $actorName : 'System';
                    ?>
                    <article class="audit-log-item">
                        <div>
                            <p class="audit-log-title"><?= e(ucwords(str_replace('_', ' ', (string) ($log['action'] ?? 'action')))) ?></p>
                            <p class="inline-note">
                                <?= e($actorName) ?>
                                <?php if (!empty($log['role'])): ?>
                                    (<?= e((string) $log['role']) ?>)
                                <?php endif; ?>
                                | <?= e((string) ($log['entity_type'] ?? 'record')) ?>
                                <?php if (!empty($log['entity_id'])): ?>
                                    #<?= e((string) $log['entity_id']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="audit-log-meta">
                            <span><?= e(date('M d, Y h:i A', strtotime((string) ($log['created_at'] ?? 'now')))) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>
