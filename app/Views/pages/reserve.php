<?php $hero = $page['hero']; ?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container reservation-section">
    <?php
    $loggedInCustomer = is_array($user ?? null) && (($user['role'] ?? 'customer') === 'customer');
    $cartIsEmpty = ($cartTotals['item_count'] ?? 0) === 0;
    $canReserve = $loggedInCustomer && !$cartIsEmpty;
    $defaultReservationDateTime = new DateTimeImmutable('+30 minutes');
    $defaultReservationMinute = (int) $defaultReservationDateTime->format('i');
    if ($defaultReservationMinute > 30) {
        $defaultReservationDateTime = $defaultReservationDateTime->modify('+1 hour');
        $defaultReservationDateTime = $defaultReservationDateTime->setTime((int) $defaultReservationDateTime->format('H'), 0);
    } elseif ($defaultReservationMinute > 0) {
        $defaultReservationDateTime = $defaultReservationDateTime->setTime((int) $defaultReservationDateTime->format('H'), 30);
    }
    $defaultReservationDate = $defaultReservationDateTime->format('Y-m-d');
    $defaultReservationTime = $defaultReservationDateTime->format('H:i');
    ?>

    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card reservation-form-card">
            <h2>Make a Reservation</h2>
            <p><?= e($page['form_intro']) ?></p>
            <?php if ($canReserve): ?>
                <form class="stack-form" action="<?= e(url('reserve')) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-grid">
                        <?php foreach ($page['form_fields'] as $field): ?>
                            <div class="form-field <?= $field['name'] === 'guests' ? 'full-width' : '' ?>">
                                <label for="<?= e($field['name']) ?>"><?= e($field['label']) ?></label>
                                <?php
                                $fieldValue = (string) match ($field['name']) {
                                    'date' => old('date', $defaultReservationDate),
                                    'time' => old('time', $defaultReservationTime),
                                    'guests' => old('guests', $field['value'] ?? '1'),
                                    'first_name' => old('first_name', $user['first_name'] ?? ''),
                                    'last_name' => old('last_name', $user['last_name'] ?? ''),
                                    'email' => old('email', $user['email'] ?? ''),
                                    'phone' => old('phone', $user['phone'] ?? ''),
                                    default => old($field['name'], $field['value'] ?? ''),
                                };
                                ?>
                                <input
                                    id="<?= e($field['name']) ?>"
                                    name="<?= e($field['name']) ?>"
                                    type="<?= e($field['type']) ?>"
                                    class="form-control <?= $field['name'] === 'date' ? 'reservation-native-date' : '' ?> <?= $field['name'] === 'time' ? 'reservation-native-time' : '' ?> <?= has_error($field['name']) ? 'is-invalid' : '' ?>"
                                    <?php if ($field['name'] === 'date'): ?>data-reservation-date-input<?php endif; ?>
                                    <?php if ($field['name'] === 'time'): ?>data-reservation-time-input<?php endif; ?>
                                    <?php if ($field['name'] === 'date'): ?>min="<?= e(date('Y-m-d')) ?>" <?php endif; ?>
                                    <?php if (!empty($field['min'])): ?>min="<?= e($field['min']) ?>" <?php endif; ?>
                                    <?php if (!empty($field['max'])): ?>max="<?= e($field['max']) ?>" <?php endif; ?>
                                    value="<?= e($fieldValue) ?>">
                                <?php if ($field['name'] === 'date'): ?>
                                    <div
                                        class="reservation-calendar"
                                        data-reservation-calendar
                                        data-selected-date="<?= e($fieldValue) ?>"
                                        data-min-date="<?= e(date('Y-m-d')) ?>"
                                        hidden>
                                        <div class="reservation-calendar__head">
                                            <button type="button" class="reservation-calendar__nav" data-calendar-prev aria-label="Previous month">&lsaquo;</button>
                                            <div>
                                                <strong data-calendar-month></strong>
                                                <span>Choose your visit date</span>
                                            </div>
                                            <button type="button" class="reservation-calendar__nav" data-calendar-next aria-label="Next month">&rsaquo;</button>
                                        </div>
                                        <div class="reservation-calendar__weekdays" aria-hidden="true">
                                            <span>Sun</span>
                                            <span>Mon</span>
                                            <span>Tue</span>
                                            <span>Wed</span>
                                            <span>Thu</span>
                                            <span>Fri</span>
                                            <span>Sat</span>
                                        </div>
                                        <div class="reservation-calendar__days" data-calendar-days></div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($field['name'] === 'time'): ?>
                                    <div
                                        class="reservation-time-picker"
                                        data-reservation-time-picker
                                        data-selected-time="<?= e($fieldValue) ?>"
                                        hidden>
                                        <div class="reservation-time-picker__head">
                                            <strong>Choose your visit time</strong>
                                            <span>30-minute slots, open all day</span>
                                        </div>
                                        <div class="reservation-time-picker__slots" data-time-slots></div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($message = field_error($field['name'])): ?>
                                    <small class="field-error"><?= e((string) $message) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="button button-primary">
                        Reserve Table
                    </button>
                </form>
            <?php elseif (!auth_check()): ?>
                <div class="feedback-login-prompt">
                    <p>Log in and add items to your cart before making a reservation.</p>
                    <a href="<?= e(url('login')) ?>" class="button button-primary">Log In to Reserve</a>
                </div>
            <?php elseif (!$loggedInCustomer): ?>
                <div class="feedback-login-prompt">
                    <p>Only customer accounts can place reservation orders.</p>
                    <a href="<?= e(auth_dashboard_path()) ?>" class="button button-primary">Back to Dashboard</a>
                </div>
            <?php else: ?>
                <div class="feedback-login-prompt">
                    <p>Add at least one item from the menu before making a reservation.</p>
                    <a href="<?= e(url('menu')) ?>" class="button button-primary">Go to Menu</a>
                </div>
            <?php endif; ?>
        </div>

        <aside class="reservation-side-panel" aria-labelledby="reservation-guide-title">
            <div class="reservation-side-panel__intro">
                <h2 id="reservation-guide-title">Before You Reserve</h2>
                <p>Keep these details ready so your table and linked order can move through smoothly.</p>
            </div>
            <?php if ($loggedInCustomer): ?>
                <article class="reservation-example reservation-cart-panel">
                    <span>Cart</span>
                    <div>
                        <h3>Cart Ready</h3>
                        <?php if ($cartItems !== []): ?>
                            <p><?= e((string) $cartTotals['item_count']) ?> item(s) selected &middot; PHP <?= e(number_format((float) $cartTotals['subtotal'], 2)) ?></p>
                            <div class="action-row reserve-sidebar-actions">
                                <a class="button button-primary" href="<?= e(url('cart?from=reservation')) ?>">Review Cart</a>
                                <a class="button button-secondary" href="<?= e(url('menu')) ?>">Add More Items</a>
                            </div>
                        <?php else: ?>
                            <p>Your cart is empty. Build it from the menu before reserving.</p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endif; ?>

            <div class="reservation-examples" aria-label="Reservation reminders">
                <?php foreach ($page['sidebar_cards'] as $card): ?>
                    <article class="reservation-example">
                        <span><?= e((string) ($card['label'] ?? $card['title'])) ?></span>
                        <div>
                            <h3><?= e($card['title']) ?></h3>
                            <p><?= e($card['body']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</section>

<section class="about-invitation reservation-invitation">
    <div class="container about-invitation__layout" data-reveal>
        <div class="about-invitation__copy">
            <h2><?= e($page['cta']['title']) ?></h2>
            <p><?= e($page['cta']['body']) ?></p>
        </div>

    </div>
</section>