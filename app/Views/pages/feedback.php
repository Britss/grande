<?php
$hero = $page['hero'];
$user = is_array($user ?? null) ? $user : null;
$defaultName = $user !== null
    ? trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')))
    : '';
$defaultEmail = $user !== null ? (string) ($user['email'] ?? '') : '';
?>
<?php require __DIR__ . '/../partials/page-hero.php'; ?>

<section class="page-section container feedback-section">
    <?php if ($status = flash('status')): ?>
        <div class="alert alert-success"><?= e((string) $status) ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <div class="split-layout">
        <div class="content-card feedback-form-card">
            <h2>Your Feedback Matters</h2>
            <p><?= e($page['form_intro']) ?></p>

            <?php if ($user !== null): ?>
                <form class="stack-form" action="<?= e(url('feedback')) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-field">
                        <label for="feedback_name">Full Name</label>
                        <input id="feedback_name" name="feedback_name" type="text" class="form-control" value="<?= e((string) old('feedback_name', $defaultName)) ?>">
                        <?php if ($message = field_error('feedback_name')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="feedback_email">Email Address</label>
                        <input id="feedback_email" name="feedback_email" type="email" class="form-control" value="<?= e((string) old('feedback_email', $defaultEmail)) ?>">
                        <?php if ($message = field_error('feedback_email')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="feedback_rating">Overall Experience Rating</label>
                            <select id="feedback_rating" name="feedback_rating" class="form-control">
                                <option value="">Select a rating</option>
                                <?php foreach (['5', '4', '3', '2', '1'] as $rating): ?>
                                    <option value="<?= e($rating) ?>" <?= old('feedback_rating') === $rating ? 'selected' : '' ?>><?= e($rating) ?> Stars</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($message = field_error('feedback_rating')): ?>
                                <p class="field-error"><?= e($message) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-field">
                            <label for="feedback_category">Feedback Category</label>
                            <select id="feedback_category" name="feedback_category" class="form-control">
                                <option value="">Select a category</option>
                                <option value="service" <?= old('feedback_category') === 'service' ? 'selected' : '' ?>>Service</option>
                                <option value="food-quality" <?= old('feedback_category') === 'food-quality' ? 'selected' : '' ?>>Food Quality</option>
                                <option value="store-cleanliness" <?= old('feedback_category') === 'store-cleanliness' ? 'selected' : '' ?>>Store Cleanliness</option>
                                <option value="website-ordering" <?= old('feedback_category') === 'website-ordering' ? 'selected' : '' ?>>Website Ordering</option>
                                <option value="reservation-experience" <?= old('feedback_category') === 'reservation-experience' ? 'selected' : '' ?>>Reservation Experience</option>
                                <option value="suggestion" <?= old('feedback_category') === 'suggestion' ? 'selected' : '' ?>>Suggestion</option>
                            </select>
                            <?php if ($message = field_error('feedback_category')): ?>
                                <p class="field-error"><?= e($message) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="feedback_body">Your Feedback</label>
                        <textarea id="feedback_body" name="feedback_body" rows="3" class="form-control"><?= e((string) old('feedback_body')) ?></textarea>
                        <?php if ($message = field_error('feedback_body')): ?>
                            <p class="field-error"><?= e($message) ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="button button-primary">Send Feedback</button>
                </form>
            <?php else: ?>
                <div class="feedback-login-prompt">
                    <p>You need to be logged in to submit feedback.</p>
                    <a href="<?= e(url('login')) ?>" class="button button-primary">Login to Submit Feedback</a>
                </div>
            <?php endif; ?>
        </div>

        <aside class="feedback-side-panel" aria-labelledby="feedback-examples-title">
            <div class="feedback-side-panel__intro">
                <h2 id="feedback-examples-title"><?= e($page['examples']['title']) ?></h2>
                <p><?= e($page['examples']['body']) ?></p>
            </div>

            <div class="feedback-examples" aria-label="Feedback examples">
                <?php foreach ($page['examples']['items'] as $example): ?>
                    <article class="feedback-example">
                        <span><?= e($example['label']) ?></span>
                        <p><?= e($example['body']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>

            <p class="feedback-side-panel__note"><?= e($page['examples']['note']) ?></p>
        </aside>
    </div>
</section>

<section class="about-invitation feedback-invitation">
    <div class="container about-invitation__layout" data-reveal>
        <div class="about-invitation__copy">
            <h2><?= e($page['cta']['title']) ?></h2>
            <p><?= e($page['cta']['body']) ?></p>
        </div>

        <div class="about-invitation__actions">
            <a class="button button-primary" href="<?= e(url('menu')) ?>">View Our Menu</a>
            <a class="button button-secondary" href="<?= e(url()) ?>">Back to Home</a>
        </div>
    </div>
</section>
