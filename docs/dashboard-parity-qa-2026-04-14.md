# Dashboard Parity QA Run - 2026-04-14

This run records the parity checks that can be completed from the local
workspace without authenticated browser sessions. Use it with
`docs/dashboard-parity-qa.md` when running the full visual pass in a browser.

## Scope

- Reviewed rewrite dashboard templates for customer, employee, and admin roles.
- Reviewed dashboard navigation and panel keys used by the shared dashboard
  JavaScript.
- Reviewed the existing parity checklist for viewport, role, and flow coverage.
- Added a repeatable local seed script for customer, employee, and admin QA
  accounts so authenticated browser runs no longer depend on undocumented
  credentials.
- Ran the QA account seed locally with the XAMPP PHP binary:
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php`.
- Added and ran `scripts/seed_dashboard_qa_workflow_data.php` with the XAMPP
  PHP binary so authenticated browser runs can start with non-empty order,
  reservation, feedback, and staff queue panels.
- Added and ran `scripts/dashboard_parity_smoke.php` with the XAMPP PHP binary
  to render the authenticated customer, employee, and admin dashboards from the
  seeded QA accounts before manual viewport checks.
- Added and ran `scripts/dashboard_viewport_readiness_audit.php` with the XAMPP
  PHP binary to render the authenticated dashboards and verify role-specific
  panel wiring plus responsive CSS markers for mobile dashboard navigation,
  horizontally safe report tables, modal height limits, compact filters, and
  dashboard assistant exclusion.
- Added `docs/dashboard-browser-parity-run-sheet.md` as the manual browser run
  record for role/viewport status, screenshot paths, interaction checks, and
  browser-only defects.
- Re-ran the dashboard QA setup and CLI checks on 2026-04-14 after the run
  sheet was added:
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php`,
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php`,
  `C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php`, and
  `C:\xampp\php\php.exe scripts\dashboard_viewport_readiness_audit.php`.
- Re-ran the dashboard QA preflight again on 2026-04-14 while preparing the
  browser parity handoff:
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php`,
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php`,
  `C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php`, and
  `C:\xampp\php\php.exe scripts\dashboard_viewport_readiness_audit.php`.
  The seed scripts reported 2 QA menu items, 4 orders, 3 reservations, and 3
  feedback records, and the authenticated smoke and viewport-readiness checks
  passed for customer, employee, and admin dashboards.
- Added and ran `scripts/dashboard_browser_run_sheet_audit.php --allow-pending`
  with the XAMPP PHP binary to validate the browser run sheet structure before
  the manual authenticated browser pass. The same script should be run without
  `--allow-pending` after the browser sheet is filled, before confirmed findings
  are copied into this dated QA record.
- Did not execute an authenticated browser pass because this workspace does not
  provide a browser automation tool.

## Static QA Results

### Customer Dashboard

- Sidebar order is Dashboard, Profile, Reservations, Orders, Feedback.
- Profile picture or initial is rendered in the sidebar and profile panel.
- Overview includes order-again, summary, quick actions, latest updates, and
  upcoming reservations blocks.
- Profile panel groups profile-picture upload, profile editing, password change,
  reset access, account summary, tips, and readiness checklist.
- Orders and reservations use modal detail hooks and dashboard section keys.
- Feedback has both sidebar access and a dedicated dashboard panel.

### Employee Dashboard

- Sidebar is limited to Overview, Payments, Orders, Reservations, Feedback,
  Reports, and Account.
- Admin-only Menu, Users, and audit controls are absent from the employee view.
- Priority Queue includes pending payments, pending reservations, and new
  feedback.
- Payment, order, reservation, and feedback panels use the shared management
  partials with employee action paths.
- Account password change uses the shared staff account partial.

### Admin Dashboard

- Sidebar includes Overview, Payments, Orders, Reservations, Menu, Users,
  Feedback, Reports, and Account.
- Needs Attention includes pending payments, pending reservations, and new
  feedback links to the matching panels.
- Menu and user management are admin-only panels.
- Reports and account panels use the shared dashboard partials.
- Staff new-order polling attributes are present on the admin workspace.

### Shared Behavior

- Navigation buttons and panels use matching `data-dashboard-target` and
  `data-dashboard-panel` keys.
- Management forms include `data-dashboard-form` and hidden `section` fields for
  AJAX refresh and form-post fallback.
- Queue badges are conditional and appear only when counts are actionable.
- Modal partials are loaded for order and reservation detail snapshots.
- Authenticated smoke coverage passed for customer, employee, and admin route
  rendering, including role-specific navigation markers and absence of
  forbidden panels/public assistant widget on dashboard pages.
- Viewport-readiness audit coverage passed for customer, employee, and admin
  route rendering, including dashboard target/panel pairs, shared responsive
  CSS breakpoints, horizontal table wrapping markers, modal dialog scaffolding,
  and public assistant exclusion on dashboard pages.
- The 2026-04-14 CLI rerun confirmed the QA accounts, non-empty workflow data,
  authenticated dashboard contracts, and viewport-readiness markers still pass
  for customer, employee, and admin dashboards.
- The latest 2026-04-14 preflight rerun again confirmed that the browser
  checklist can start from seeded, non-empty customer, employee, and admin
  dashboards without first repairing account, data, route, or responsive-marker
  setup.
- The browser run-sheet audit now confirms the handoff sheet has the required
  role/viewport matrix, screenshot rows, and completion fields while still
  allowing pending manual entries during preflight.

## Intentional Differences

- Old destructive delete flows remain represented by archive/deactivation
  behavior in the rewrite. This is intentional to preserve historical orders,
  reservations, feedback, notifications, and audit references.
- Completed order and reservation email summaries are sent in the email body
  rather than as separate text attachments because the current lightweight
  mailer supports body content only.

## Remaining Browser QA

The account seed, workflow data seed, authenticated dashboard smoke test, and
viewport-readiness audit have been run for this local database. Sign in with
the QA accounts documented in `docs/dashboard-parity-qa.md`, use
`docs/dashboard-browser-parity-run-sheet.md` to record screenshots and viewport
status, and complete the browser checklist against the seeded non-empty
dashboard panels. The browser pass still needs to verify:

- Desktop, laptop, tablet, and mobile viewport rendering.
- Mouse, keyboard, and touch access to every dashboard section.
- No horizontal overflow in tables, filters, forms, modals, toasts, or fixed
  notifications.
- AJAX-enhanced mutations preserve the same visible state as fallback form
  submissions.
- Dashboard screenshots remain recognizably aligned with the legacy `grandego`
  dashboards.
