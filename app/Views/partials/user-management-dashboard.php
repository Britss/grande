<?php
/** @var array $userManagementStats */
/** @var array $manageableUsers */
?>
<section class="page-section container">
    <div class="payment-review-header">
        <div>
            <p class="eyebrow">User Management</p>
            <h2>Create and manage staff accounts</h2>
        </div>
        <p class="payment-review-note">Admin can add staff accounts here and manage customer or staff access without leaving the rebuilt app.</p>
    </div>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">Admins</p>
            <h2><?= e((string) ($userManagementStats['admin_count'] ?? 0)) ?></h2>
            <p>Accounts with full back-office access.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Employees</p>
            <h2><?= e((string) ($userManagementStats['employee_count'] ?? 0)) ?></h2>
            <p>Operations staff accounts currently in the system.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Customers</p>
            <h2><?= e((string) ($userManagementStats['customer_count'] ?? 0)) ?></h2>
            <p>Registered customer accounts in `grande.users`.</p>
        </article>
    </div>

    <div class="dashboard-filter-bar" data-dashboard-filter="users">
        <div class="dashboard-filter-field dashboard-filter-field--wide">
            <label for="user-filter-search">Search users</label>
            <input id="user-filter-search" type="search" class="form-control" placeholder="Name, email, phone" data-filter-search>
        </div>
        <div class="dashboard-filter-field">
            <label for="user-filter-status">Account status</label>
            <select id="user-filter-status" class="form-control" data-filter-status>
                <option value="all">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="dashboard-filter-field">
            <label for="user-filter-flow">Role</label>
            <select id="user-filter-flow" class="form-control" data-filter-flow>
                <option value="all">All roles</option>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
                <option value="customer">Customer</option>
            </select>
        </div>
        <button type="button" class="button button-secondary button-small" data-filter-reset>Reset</button>
    </div>

    <div class="management-grid">
        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Create Staff Account</h3>
            </div>
            <form method="post" action="<?= e(url('/dashboard/admin/users')) ?>" class="stack-form" data-dashboard-form="users">
                <?= csrf_field() ?>
                <input type="hidden" name="user_action" value="create_staff">
                <input type="hidden" name="section" value="users">

                <div class="form-grid">
                    <div class="form-field">
                        <label for="staff-first-name">First Name</label>
                        <input id="staff-first-name" name="first_name" type="text" class="form-control">
                    </div>
                    <div class="form-field">
                        <label for="staff-last-name">Last Name</label>
                        <input id="staff-last-name" name="last_name" type="text" class="form-control">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="staff-email">Email</label>
                        <input id="staff-email" name="email" type="email" class="form-control">
                    </div>
                    <div class="form-field">
                        <label for="staff-phone">Phone</label>
                        <input id="staff-phone" name="phone" type="text" class="form-control" placeholder="09XXXXXXXXX">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="staff-password">Password</label>
                        <input id="staff-password" name="password" type="password" class="form-control">
                    </div>
                    <div class="form-field">
                        <label for="staff-confirm-password">Confirm Password</label>
                        <input id="staff-confirm-password" name="confirm_password" type="password" class="form-control">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="staff-role">Role</label>
                        <select id="staff-role" name="role" class="form-control">
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <label class="checkbox-line">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Activate account immediately</span>
                </label>

                <button type="submit" class="button button-primary">Create Staff User</button>
            </form>
        </article>

        <div class="management-stack">
            <?php foreach ($manageableUsers as $managedUser): ?>
                <?php
                $managedUserName = trim(((string) ($managedUser['first_name'] ?? '')) . ' ' . ((string) ($managedUser['last_name'] ?? '')));
                $managedUserStatus = (int) ($managedUser['is_active'] ?? 0) === 1 ? 'active' : 'inactive';
                $managedUserRole = (string) ($managedUser['role'] ?? '');
                ?>
                <article
                    class="content-card management-card"
                    data-filter-item
                    data-filter-status="<?= e($managedUserStatus) ?>"
                    data-filter-flow="<?= e($managedUserRole) ?>"
                    data-filter-text="<?= e(strtolower(trim($managedUserName . ' ' . ($managedUser['email'] ?? '') . ' ' . ($managedUser['phone'] ?? '')))) ?>"
                >
                    <form method="post" action="<?= e(url('/dashboard/admin/users')) ?>" class="stack-form" data-dashboard-form="users">
                        <?= csrf_field() ?>
                        <input type="hidden" name="user_action" value="update_user">
                        <input type="hidden" name="section" value="users">
                        <input type="hidden" name="user_id" value="<?= e((string) ($managedUser['id'] ?? 0)) ?>">

                        <div class="management-subcard__header">
                            <div>
                                <h3><?= e(trim(((string) ($managedUser['first_name'] ?? '')) . ' ' . ((string) ($managedUser['last_name'] ?? '')))) ?></h3>
                                <p class="inline-note"><?= e((string) ($managedUser['email'] ?? '')) ?></p>
                            </div>
                            <span class="status-pill status-pill--<?= (int) ($managedUser['is_active'] ?? 0) === 1 ? 'confirmed' : 'cancelled' ?>">
                                <?= (int) ($managedUser['is_active'] ?? 0) === 1 ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label>First Name</label>
                                <input name="first_name" type="text" class="form-control" value="<?= e((string) ($managedUser['first_name'] ?? '')) ?>">
                            </div>
                            <div class="form-field">
                                <label>Last Name</label>
                                <input name="last_name" type="text" class="form-control" value="<?= e((string) ($managedUser['last_name'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control" value="<?= e((string) ($managedUser['email'] ?? '')) ?>">
                            </div>
                            <div class="form-field">
                                <label>Phone</label>
                                <input name="phone" type="text" class="form-control" value="<?= e((string) ($managedUser['phone'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label>Role</label>
                                <select name="role" class="form-control">
                                    <?php foreach (['customer', 'employee', 'admin'] as $roleOption): ?>
                                        <option value="<?= e($roleOption) ?>" <?= ($managedUser['role'] ?? '') === $roleOption ? 'selected' : '' ?>>
                                            <?= e(ucfirst($roleOption)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Created</label>
                                <input type="text" class="form-control" value="<?= e(date('M d, Y', strtotime((string) ($managedUser['created_at'] ?? 'now')))) ?>" disabled>
                            </div>
                        </div>

                        <div class="management-inline-actions">
                            <label class="checkbox-line">
                                <input type="checkbox" name="is_active" value="1" <?= (int) ($managedUser['is_active'] ?? 0) === 1 ? 'checked' : '' ?>>
                                <span>Account active</span>
                            </label>
                            <button type="submit" class="button button-secondary button-small">Save User</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>

            <div class="content-card payment-review-empty" data-filter-empty hidden>
                <h3>No users match those filters.</h3>
                <p>Adjust the search, role, or account status filter to review more users.</p>
            </div>
        </div>
    </div>
</section>
