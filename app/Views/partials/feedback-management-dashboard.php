<?php
/** @var array $feedbackStats */
/** @var array $manageableFeedback */
/** @var string $feedbackActionPath */
?>
<section class="page-section container">
    <div class="payment-review-header">
        <div>
            <p class="eyebrow">Feedback Inbox</p>
            <h2>Review customer feedback</h2>
        </div>
        <p class="payment-review-note">Customers now submit feedback into `grande`, and staff can work the inbox here.</p>
    </div>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">New</p>
            <h2><?= e((string) ($feedbackStats['new_feedback'] ?? 0)) ?></h2>
            <p>Fresh submissions that still need a first review.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">In Review</p>
            <h2><?= e((string) ($feedbackStats['in_review_feedback'] ?? 0)) ?></h2>
            <p>Feedback already being checked by the team.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Average Rating</p>
            <h2><?= e(number_format((float) ($feedbackStats['average_rating'] ?? 0), 1)) ?></h2>
            <p>Average submitted rating across the stored feedback records.</p>
        </article>
    </div>

    <?php if ($manageableFeedback === []): ?>
        <div class="content-card payment-review-empty">
            <h3>No feedback submissions yet.</h3>
            <p>New customer comments will appear here after feedback starts coming in.</p>
        </div>
    <?php else: ?>
        <div class="management-stack">
            <?php foreach ($manageableFeedback as $feedback): ?>
                <article class="content-card management-card">
                    <div class="management-subcard__header">
                        <div>
                            <h3><?= e((string) ($feedback['name'] ?? 'Guest')) ?></h3>
                            <p class="inline-note"><?= e((string) ($feedback['email'] ?? '')) ?> | <?= e(ucwords(str_replace('-', ' ', (string) ($feedback['category'] ?? 'general')))) ?></p>
                        </div>
                        <div class="management-inline-actions management-inline-actions--compact">
                            <span class="status-pill status-pill--<?= e((string) ($feedback['status'] ?? 'new')) ?>"><?= e(ucfirst((string) ($feedback['status'] ?? 'new'))) ?></span>
                            <span class="status-pill status-pill--confirmed"><?= e((string) ($feedback['rating'] ?? 0)) ?>/5</span>
                        </div>
                    </div>

                    <p class="management-message"><?= e((string) ($feedback['message'] ?? '')) ?></p>

                    <div class="payment-review-support">
                        <p><strong>Submitted:</strong> <?= e(date('M d, Y h:i A', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?></p>
                        <?php if (!empty($feedback['user_id'])): ?>
                            <p><strong>Linked account:</strong> <?= e(trim(((string) ($feedback['user_first_name'] ?? '')) . ' ' . ((string) ($feedback['user_last_name'] ?? '')))) ?></p>
                        <?php else: ?>
                            <p><strong>Linked account:</strong> Guest submission</p>
                        <?php endif; ?>
                    </div>

                    <form method="post" action="<?= e(url($feedbackActionPath)) ?>" class="management-inline-form" data-dashboard-form="feedback">
                        <?= csrf_field() ?>
                        <input type="hidden" name="feedback_id" value="<?= e((string) ($feedback['id'] ?? 0)) ?>">
                        <input type="hidden" name="section" value="feedback">
                        <label class="field-label" for="feedback-status-<?= e((string) ($feedback['id'] ?? 0)) ?>">Status</label>
                        <select id="feedback-status-<?= e((string) ($feedback['id'] ?? 0)) ?>" name="status" class="form-control">
                            <?php foreach (['new', 'in_review', 'resolved', 'archived'] as $statusOption): ?>
                                <option value="<?= e($statusOption) ?>" <?= ($feedback['status'] ?? 'new') === $statusOption ? 'selected' : '' ?>>
                                    <?= e(ucfirst(str_replace('_', ' ', $statusOption))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="button button-primary button-small">Update Feedback</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
