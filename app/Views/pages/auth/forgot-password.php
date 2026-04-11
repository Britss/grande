<section class="page-section container auth-shell">
    <div class="split-layout auth-layout">
        <aside class="content-card content-card--feature auth-aside">
            <p class="eyebrow">Account Recovery</p>
            <h1>Reset your password.</h1>
            <p class="lead">
                Enter your account email and Grande will send a time-limited link for setting a new password.
            </p>
            <ul class="plain-list auth-points">
                <li>Links expire after 30 minutes</li>
                <li>Only the latest reset link can be used</li>
                <li>Inactive accounts can recover passwords, but login still follows account status</li>
            </ul>
        </aside>

        <div class="content-card auth-card">
            <p class="eyebrow">Authentication</p>
            <h2>Forgot Password</h2>
            <p class="auth-copy">Use the email address attached to your Grande account.</p>

            <?php if ($status = flash('status')): ?>
                <div class="alert alert-success"><?= e((string) $status) ?></div>
            <?php endif; ?>

            <?php if ($info = flash('info')): ?>
                <div class="alert alert-info"><?= e((string) $info) ?></div>
            <?php endif; ?>

            <?php if ($previewUrl = flash('password_reset_preview_url')): ?>
                <div class="alert alert-info"><?= e((string) $previewUrl) ?></div>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <form class="stack-form auth-form" method="post" action="<?= e(url('password/forgot')) ?>">
                <?= csrf_field() ?>

                <div class="form-field">
                    <label for="email">Email address</label>
                    <input
                        id="email"
                        class="<?= has_error('email') ? 'is-invalid' : '' ?>"
                        type="email"
                        name="email"
                        value="<?= e((string) old('email')) ?>"
                        autocomplete="email"
                        required
                    >
                    <?php if ($message = field_error('email')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <button class="button button-primary auth-submit" type="submit">Send Reset Link</button>
            </form>

            <p class="auth-meta">
                Remembered it?
                <a href="<?= e(url('login')) ?>">Return to login</a>.
            </p>
        </div>
    </div>
</section>
