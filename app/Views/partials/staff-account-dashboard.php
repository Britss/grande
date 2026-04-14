<?php
$staffRole = $staffRole ?? (string) ($user['role'] ?? 'employee');
$passwordActionPath = $passwordActionPath ?? '/dashboard/' . $staffRole . '/password';
$staffName = trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')));
?>
<div class="dashboard-section-heading">
    <span class="dashboard-kicker">Account Access</span>
    <h2>Keep your staff sign-in current.</h2>
    <p class="lead">Change your own password after confirming the one you use now.</p>
</div>

<div class="dashboard-layout dashboard-layout--staff">
    <div class="dashboard-main-column">
        <article class="content-card dashboard-card">
            <div class="dashboard-card__header">
                <h2>Password</h2>
            </div>
            <form action="<?= e(url(ltrim($passwordActionPath, '/'))) ?>" method="post" class="form-grid" data-dashboard-form="account">
                <?= csrf_field() ?>
                <input type="hidden" name="section" value="account">

                <div class="form-field">
                    <label for="<?= e($staffRole) ?>_current_password">Current Password</label>
                    <input
                        type="password"
                        id="<?= e($staffRole) ?>_current_password"
                        name="current_password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="<?= e($staffRole) ?>_new_password">New Password</label>
                    <input
                        type="password"
                        id="<?= e($staffRole) ?>_new_password"
                        name="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="<?= e($staffRole) ?>_confirm_password">Confirm New Password</label>
                    <input
                        type="password"
                        id="<?= e($staffRole) ?>_confirm_password"
                        name="confirm_password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="button button-primary">Update Password</button>
                    <a href="<?= e(url('password/forgot')) ?>" class="button button-secondary button-small">Use Reset Link</a>
                </div>
            </form>
        </article>
    </div>

    <div class="dashboard-side-column">
        <article class="content-card dashboard-card">
            <div class="dashboard-card__header">
                <h2>Signed In As</h2>
            </div>
            <div class="dashboard-summary-list">
                <div class="detail-row">
                    <span class="label">Name</span>
                    <span class="value"><?= e($staffName !== '' ? $staffName : 'Staff') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Email</span>
                    <span class="value"><?= e((string) ($user['email'] ?? '')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Role</span>
                    <span class="value"><?= e(ucfirst((string) ($user['role'] ?? $staffRole))) ?></span>
                </div>
            </div>
        </article>
    </div>
</div>
