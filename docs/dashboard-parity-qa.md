# Dashboard Parity QA Checklist

Use this checklist for the browser-level dashboard comparison against the legacy
`grandego` implementation. The goal is visual and flow parity, not pixel-perfect
copying where the rewrite intentionally uses safer workflows or cleaner data
ownership.

## Reference Files

- Legacy customer dashboard: `c:\xampp\htdocs\grandego\pages\dashboards\customer-dashboard.php`
- Legacy employee dashboard: `c:\xampp\htdocs\grandego\pages\dashboards\employee-dashboard.php`
- Legacy admin dashboard: `c:\xampp\htdocs\grandego\pages\dashboards\admin-dashboard.php`
- Legacy shared dashboard CSS: `c:\xampp\htdocs\grandego\assets\css\dashboards\dashboard.css`
- Legacy admin dashboard CSS: `c:\xampp\htdocs\grandego\assets\css\dashboards\admin.css`
- Legacy shared dashboard JS: `c:\xampp\htdocs\grandego\assets\js\dashboards\dashboard.js`
- Legacy admin dashboard JS: `c:\xampp\htdocs\grandego\assets\js\dashboards\admin-dashboard.js`
- Legacy employee dashboard JS: `c:\xampp\htdocs\grandego\assets\js\dashboards\employee-dashboard.js`

## Rewrite Routes

- Customer: `/dashboard/customer`
- Employee: `/dashboard/employee`
- Admin: `/dashboard/admin`

## Seeded QA Accounts

Run this before the authenticated browser pass if the local `grande` database
does not already have dashboard users:

```powershell
php scripts/seed_dashboard_qa_accounts.php
```

If PHP is not on PATH in a default XAMPP shell, run the bundled binary directly:

```powershell
C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php
```

The script creates or refreshes these active accounts:

- Customer: `qa.customer@grande.local`
- Employee: `qa.employee@grande.local`
- Admin: `qa.admin@grande.local`

Default password: `GrandeQA#2026`

Set `QA_SEED_PASSWORD` before running the script to use a different local QA
password. The script only upserts users and does not create orders,
reservations, feedback, or menu data.

To seed representative non-empty dashboard panels for browser parity checks,
run:

```powershell
php scripts/seed_dashboard_qa_workflow_data.php
```

Or, with the bundled XAMPP PHP binary:

```powershell
C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php
```

The workflow seeder also refreshes the QA accounts, then creates or refreshes
QA-only menu items, direct orders, a reservation-linked order, reservations,
and feedback records. It removes previous QA workflow rows using `QA-` order
numbers, `[QA]` feedback messages, and the documented QA customer email before
recreating the sample data.

Before opening the browser checklist, run the authenticated dashboard contract
smoke test:

```powershell
php scripts/dashboard_parity_smoke.php
```

Or, with the bundled XAMPP PHP binary:

```powershell
C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php
```

The smoke test signs in as the seeded customer, employee, and admin accounts in
CLI session context, renders each dashboard route, and verifies the expected
role-specific navigation and account markers while checking that forbidden
dashboard panels and the public assistant widget are absent.

## Viewports

Run each dashboard through these viewport widths:

- Desktop: 1440px wide
- Laptop: 1280px wide
- Tablet: 768px wide
- Mobile: 390px wide

## Shared Dashboard Checks

- Sidebar uses the same brand-first layout rhythm as the legacy dashboard.
- Sidebar profile block is visible, compact, and does not wrap awkwardly.
- Active navigation state is clear and follows the clicked dashboard section.
- Queue badges appear only when the count is actionable.
- Mobile navigation becomes horizontally usable without hiding section buttons.
- Welcome area, stat cards, and content cards stay compact.
- Card spacing and panel density are closer to the legacy dashboard than to a landing-page layout.
- Forms, filter bars, and action rows do not overflow at mobile widths.
- Modal buttons and close controls are reachable on desktop and mobile.
- AJAX-enhanced actions keep the same visual result as fallback form posts.
- Toasts and fixed notifications do not cover primary controls.

## Customer Dashboard

- Navigation order remains Dashboard, Profile, Reservations, Orders, Feedback.
- Profile image or initial renders in the sidebar without layout shift.
- Overview shows order-again, summary, quick actions, latest updates, and upcoming reservations in a familiar hierarchy.
- Empty states point customers to menu, reservation, or feedback actions.
- Order and reservation detail modals match the legacy expectation for readable line-item snapshots.
- Cancellation buttons appear only for eligible pending direct orders or reservations.
- Reorder action is visible from the latest order and order-history records.
- Profile editing, profile-picture upload, and password change controls remain grouped in the Profile panel.
- Customer notification toast and latest-updates feed appear without crowding the sidebar or hero.

## Employee Dashboard

- Navigation is limited to Overview, Payments, Orders, Reservations, Feedback, Reports, and Account.
- Priority Queue mirrors the legacy operational focus with pending payments, reservations, and new feedback.
- Employee cannot see admin-only user management, menu management, audit browsing, or admin reports.
- Payment review, order management, reservation management, and feedback panels use compact filters.
- Queue cards preserve status pills, timestamps, customer context, and primary action controls.
- Reports panel stays queue-oriented rather than business-analytics-heavy.
- New-order polling toast links to the right operational panel.
- Account password-change panel follows the same staff dashboard styling.

## Admin Dashboard

- Navigation includes Overview, Payments, Orders, Reservations, Menu, Users, Feedback, Reports, and Account.
- Needs Attention queue jumps to pending payment, reservation, and feedback sections.
- Admin overview cards remain compact and operational.
- Menu management filters, upload controls, and item forms do not crowd mobile layouts.
- User management keeps role/status filters and activation/deactivation controls clear.
- Reports include date-range controls, print/save guidance, chart-style summaries, and detail tables.
- Audit log browsing is readable at desktop widths and horizontally safe on small screens.
- Admin new-order polling toast and queue badges refresh without duplicating content.

## Pass Criteria

- No dashboard section is unreachable by mouse, keyboard, or touch.
- No card, table, form, modal, toast, or fixed element causes horizontal page overflow at the target viewports.
- Role-specific dashboards expose only the intended panels and actions.
- Visual structure remains recognizably aligned with the legacy `grandego` dashboards.
- Document any intentional difference in `PLAN.md` instead of treating it as an unresolved parity gap.
