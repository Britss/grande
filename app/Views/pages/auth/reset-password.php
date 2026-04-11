<section class="page-section container auth-shell">
    <div class="split-layout auth-layout">
        <aside class="content-card content-card--feature auth-aside">
            <p class="eyebrow">Account Recovery</p>
            <h1>Set a new password.</h1>
            <p class="lead">
                Choose a password that keeps your account ready for orders, reservations, and dashboard access.
            </p>
            <ul class="plain-list auth-points">
                <li>Use at least 8 characters</li>
                <li>Include uppercase, lowercase, and a number</li>
                <li>The reset link is consumed after a successful change</li>
            </ul>
        </aside>

        <div class="content-card auth-card">
            <p class="eyebrow">Authentication</p>
            <h2>Reset Password</h2>
            <p class="auth-copy">Enter and confirm your new account password.</p>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <form class="stack-form auth-form" method="post" action="<?= e(url('password/reset')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e((string) ($token ?? '')) ?>">

                <div class="form-field">
                    <label for="password">New password</label>
                    <div class="password-field">
                        <input
                            id="password"
                            class="<?= has_error('password') ? 'is-invalid' : '' ?>"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <button
                            class="password-toggle"
                            type="button"
                            data-password-toggle
                            data-target="password"
                            aria-controls="password"
                            aria-label="Show password"
                        >Show</button>
                    </div>
                    <?php if ($message = field_error('password')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="confirm_password">Confirm password</label>
                    <div class="password-field">
                        <input
                            id="confirm_password"
                            class="<?= has_error('confirm_password') ? 'is-invalid' : '' ?>"
                            type="password"
                            name="confirm_password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <button
                            class="password-toggle"
                            type="button"
                            data-password-toggle
                            data-target="confirm_password"
                            aria-controls="confirm_password"
                            aria-label="Show password"
                        >Show</button>
                    </div>
                    <?php if ($message = field_error('confirm_password')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <button class="button button-primary auth-submit" type="submit">Update Password</button>
            </form>
        </div>
    </div>
</section>
