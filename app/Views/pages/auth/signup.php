<section class="page-section container auth-shell">
    <div class="split-layout auth-layout">
        <aside class="content-card content-card--feature auth-aside">
            <p class="eyebrow">New Customer</p>
            <h1>Create your account.</h1>
            <p class="lead">
                This is the first database-backed customer flow in the rebuild. New signups are stored in `grande.users`
                and sent to login after validation passes.
            </p>
            <ul class="plain-list auth-points">
                <li>Customer accounts only in this first pass</li>
                <li>Email and phone duplicate checks</li>
                <li>Passwords hashed before insert</li>
            </ul>
        </aside>

        <div class="content-card auth-card">
            <p class="eyebrow">Authentication</p>
            <h2>Sign Up</h2>
            <p class="auth-copy">Submit your details first, then verify your email before the account is created.</p>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-error"><?= e((string) $error) ?></div>
            <?php endif; ?>

            <form class="stack-form auth-form" method="post" action="<?= e(url('signup')) ?>">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="first_name">First name</label>
                        <input
                            id="first_name"
                            class="<?= has_error('first_name') ? 'is-invalid' : '' ?>"
                            type="text"
                            name="first_name"
                            value="<?= e((string) old('first_name')) ?>"
                            autocomplete="given-name"
                            minlength="2"
                            maxlength="50"
                            pattern="[A-Za-z][A-Za-z' -]*"
                            required
                        >
                        <?php if ($message = field_error('first_name')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="last_name">Last name</label>
                        <input
                            id="last_name"
                            class="<?= has_error('last_name') ? 'is-invalid' : '' ?>"
                            type="text"
                            name="last_name"
                            value="<?= e((string) old('last_name')) ?>"
                            autocomplete="family-name"
                            minlength="2"
                            maxlength="50"
                            pattern="[A-Za-z][A-Za-z' -]*"
                            required
                        >
                        <?php if ($message = field_error('last_name')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field full-width">
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

                    <div class="form-field full-width">
                        <label for="phone">Phone number</label>
                        <input
                            id="phone"
                            class="<?= has_error('phone') ? 'is-invalid' : '' ?>"
                            type="tel"
                            name="phone"
                            value="<?= e((string) old('phone')) ?>"
                            placeholder="09XXXXXXXXX"
                            autocomplete="tel"
                            inputmode="numeric"
                            maxlength="11"
                            pattern="09[0-9]{9}"
                            required
                        >
                        <?php if ($message = field_error('phone')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                        <p class="field-note">Use your active Philippine mobile number in the format `09XXXXXXXXX`.</p>
                    </div>

                    <div class="form-field full-width">
                        <label for="password">Password</label>
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

                    <div class="form-field full-width">
                        <label for="confirm_password">Confirm password</label>
                        <div class="password-field">
                            <input
                                id="confirm_password"
                                class="<?= has_error('confirm_password') ? 'is-invalid' : '' ?>"
                                type="password"
                                name="confirm_password"
                                autocomplete="new-password"
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

                    <div class="form-field full-width">
                        <p class="field-note">Use at least 8 characters with uppercase, lowercase, and a number.</p>
                    </div>
                </div>

                <div class="auth-check">
                    <label class="auth-check__label" for="terms">
                        <input
                            id="terms"
                            type="checkbox"
                            name="terms"
                            value="1"
                            <?= old('terms') ? 'checked' : '' ?>
                            required
                        >
                        <span>
                            I agree to the <a href="#terms-conditions">Terms &amp; Conditions</a> and
                            <a href="#privacy-policy">Privacy Policy</a>.
                        </span>
                    </label>
                    <?php if ($message = field_error('terms')): ?>
                        <p class="field-error"><?= e($message) ?></p>
                    <?php endif; ?>
                </div>

                <button class="button button-primary auth-submit" type="submit">Send Verification Code</button>
            </form>

            <p class="auth-meta">
                Already registered?
                <a href="<?= e(url('login')) ?>">Login instead</a>.
            </p>
        </div>
    </div>

    <div class="content-grid auth-legal-grid">
        <article id="terms-conditions" class="content-card auth-legal-card">
            <p class="eyebrow">Inspired By GrandeGo</p>
            <h2>Terms &amp; Conditions</h2>
            <p class="auth-copy">
                This follows the same account, ordering, and reservation expectations used in the current GrandeGo
                experience, condensed into a readable signup summary.
            </p>
            <ol class="auth-legal-list">
                <li>You must provide accurate, current, and complete account information.</li>
                <li>You are responsible for keeping your login credentials confidential.</li>
                <li>Orders and reservations remain subject to availability and store confirmation.</li>
                <li>Grande may refuse, cancel, or adjust requests that are incomplete, abusive, or clearly invalid.</li>
                <li>Use of the website must stay lawful and must not interfere with the service or other users.</li>
            </ol>
        </article>

        <article id="privacy-policy" class="content-card auth-legal-card">
            <p class="eyebrow">Inspired By GrandeGo</p>
            <h2>Privacy Policy</h2>
            <p class="auth-copy">
                The signup flow stores the same basic customer details the old site relied on for accounts,
                reservations, and service communication.
            </p>
            <ol class="auth-legal-list">
                <li>We collect your name, email, phone number, and account credentials.</li>
                <li>These details are used for authentication, reservations, support, and service updates.</li>
                <li>We do not ask for unnecessary profile information in this first rebuild phase.</li>
                <li>Your password is stored as a hash, not as plain text.</li>
                <li>You can contact Grande using the details below for questions about account data.</li>
            </ol>
            <div class="auth-legal-contact">
                <p>Email: grande.pandesalcoffee.main@gmail.com</p>
                <p>Phone: +63 954 247 8073</p>
                <p>Address: Sindalan, San Fernando, Pampanga</p>
            </div>
        </article>
    </div>
</section>
