<section class="page-section container auth-shell">
    <div class="split-layout auth-layout">
        <aside class="content-card content-card--feature auth-aside">
            <p class="eyebrow">Email Verification</p>
            <h1>Confirm your email first.</h1>
            <p class="lead">
                Grande now creates the account only after the verification code is confirmed. This keeps the signup
                flow closer to a real production-ready account system.
            </p>
            <ul class="plain-list auth-points">
                <li>6-digit verification code</li>
                <li>Code expires in 15 minutes</li>
                <li>Account is created only after successful verification</li>
            </ul>
        </aside>

        <div class="content-card auth-card">
            <p class="eyebrow">Authentication</p>
            <h2>Verify Email</h2>
            <p class="auth-copy">Enter the code sent to your email to finish creating your account.</p>

            <?php if ($status = flash('status')): ?>
                <div class="alert alert-success"><?= e((string) $status) ?></div>
            <?php endif; ?>

            <?php if (!empty($localPreviewCode)): ?>
                <p class="auth-dev-note">Preview code: <strong><?= e((string) $localPreviewCode) ?></strong></p>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <form class="stack-form auth-form" method="post" action="<?= e(url('signup/verify')) ?>">
                <?= csrf_field() ?>

                <div class="form-field">
                    <label for="email">Email address</label>
                    <input
                        id="email"
                        class="<?= has_error('email') ? 'is-invalid' : '' ?>"
                        type="email"
                        name="email"
                        value="<?= e((string) ($email ?? old('email'))) ?>"
                        autocomplete="email"
                        required
                    >
                    <?php if ($message = field_error('email')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="verification_code">Verification code</label>
                    <input
                        id="verification_code"
                        class="verification-code-input <?= has_error('verification_code') ? 'is-invalid' : '' ?>"
                        type="text"
                        name="verification_code"
                        value="<?= e((string) old('verification_code')) ?>"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="123456"
                        required
                    >
                    <?php if ($message = field_error('verification_code')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <button class="button button-primary auth-submit" type="submit">Verify And Create Account</button>
            </form>

            <form class="stack-form auth-inline-form" method="post" action="<?= e(url('signup/verify/resend')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="email" value="<?= e((string) ($email ?? old('email'))) ?>">
                <button class="button button-secondary auth-submit" type="submit">Resend Code</button>
            </form>

            <p class="auth-meta">
                Need to change your details?
                <a href="<?= e(url('signup')) ?>">Go back to signup</a>.
            </p>
        </div>
    </div>
</section>
