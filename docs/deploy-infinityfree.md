# Deploying Grande to InfinityFree

This project is a plain PHP/MySQL app. There is no Node or Composer build step.

## 1. Create the hosting account

1. In InfinityFree, create a hosting account for your free subdomain or custom domain.
2. Open the account control panel and note the `htdocs` path, FTP username, FTP host, and MySQL section.
3. Create a MySQL database. Copy the database host, database name, username, and password.

## 2. Configure production credentials

1. Copy `app/config.local.example.php` to `app/config.local.php`.
2. Replace these values with the database values from InfinityFree:
   - `database.host`
   - `database.name`
   - `database.username`
   - `database.password`
3. Keep `app/config.local.php` private. It is ignored by git.

InfinityFree commonly does not provide project-level environment variables, so `app/config.local.php` is the deployment override for this app.

## 3. Upload files

Upload the contents of this repository into the site's `htdocs` directory:

- `.htaccess`
- `index.php`
- `app/`
- `database/`
- `public/`
- `routes/`
- `storage/`

Do not upload `.git/`, `.agents/`, `docs/`, or local-only notes unless you need them on the server.

Make sure these directories exist and are writable by PHP:

- `storage/logs`
- `storage/sessions`
- `storage/uploads`
- `public/uploads`
- `public/uploads/receipts`
- `public/uploads/menu-items`
- `public/uploads/profile-pictures`

## 4. Import the database

In InfinityFree phpMyAdmin, select the database and import `database/schema.infinityfree.sql`.
Then import `database/seed_menu_catalog.sql` so the public menu and ordering flow have live catalog rows.

If you prefer importing the migration files individually, import them in this order:

1. `database/migrations/2026_04_10_000001_create_users.sql`
2. `database/migrations/2026_04_10_000002_create_signup_verifications.sql`
3. `database/migrations/2026_04_10_000003_create_menu_items.sql`
4. `database/migrations/2026_04_10_000004_create_menu_item_sizes.sql`
5. `database/migrations/2026_04_11_000005_create_cart_items.sql`
6. `database/migrations/2026_04_11_000006_create_reservations.sql`
7. `database/migrations/2026_04_11_000007_create_orders.sql`
8. `database/migrations/2026_04_11_000008_create_order_items.sql`
9. `database/migrations/2026_04_11_000009_create_feedback.sql`
10. `database/migrations/2026_04_11_000010_create_audit_logs.sql`
11. `database/migrations/2026_04_11_000011_create_password_resets.sql`
12. `database/migrations/2026_04_14_000012_create_customer_notifications.sql`

After importing the schema, run any seed/import SQL you use for accounts. The included `database/seed_menu_catalog.sql` is idempotent: it adds the starter menu only when matching menu items or sizes do not already exist.

## 5. Check routing

The root `.htaccess` routes requests to `index.php`, and `index.php` loads `public/index.php`. The app should work from the domain root after upload.

If the site is placed inside a subdirectory, the app detects the base path from `SCRIPT_NAME`, but deploying to the domain root is recommended.

## 6. Mail notes

The default production example uses PHP `mail()`. If InfinityFree blocks or limits outgoing mail on your account, signup verification and status emails may fall back poorly. In that case, update `app/config.local.php` with SMTP credentials and set:

```php
'smtp_enabled' => true,
'use_php_mail' => false,
```

## 7. Smoke test

After upload, test these flows:

- Home, menu, about, feedback, reserve pages load.
- Signup can create a pending verification.
- Login works for seeded admin/employee/customer accounts.
- Cart, checkout, reservation, and receipt upload write to the database.
- Dashboard pages load for each role.
