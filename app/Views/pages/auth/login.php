<section class="page-section container auth-shell">
    <div class="split-layout auth-layout">
        <aside class="content-card content-card--feature auth-aside">
            <p class="eyebrow">Grande Account</p>
            <h1>Welcome back.</h1>
            <p class="lead">
                Sign in to manage your account, view your reservations, and continue with your orders at Grande.
            </p>
            <ul class="plain-list auth-points">
                <li>Access your dashboard and account details</li>
                <li>View your reservations and activity in one place</li>
                <li>Protected sign-in with secure validation</li>
            </ul>
        </aside>

        <div class="content-card auth-card">
            <p class="eyebrow">Authentication</p>
            <h2>Login</h2>
            <p class="auth-copy">Enter the email address and password linked to your Grande account.</p>

            <?php if ($status = flash('status')): ?>
                <div class="alert alert-success"><?= e((string) $status) ?></div>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <form class="stack-form auth-form" method="post" action="<?= e(url('login')) ?>">
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

                <div class="form-field">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input
                            id="password"
                            class="<?= has_error('password') ? 'is-invalid' : '' ?>"
                            type="password"
                            name="password"
                            autocomplete="current-password"
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

                <button class="button button-primary auth-submit" type="submit">Login</button>
            </form>

            <p class="auth-meta">
                <a href="<?= e(url('password/forgot')) ?>">Forgot password?</a>
            </p>

            <p class="auth-meta">
                No account yet?
                <a href="<?= e(url('signup')) ?>">Create one here</a>.
            </p>
        </div>
    </div>
</section>
