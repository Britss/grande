# Dashboard Parity QA Run - 2026-04-14

This run records the parity checks that can be completed from the local
workspace without authenticated browser sessions. Use it with
`docs/dashboard-parity-qa.md` when running the full visual pass in a browser.

## Scope

- Reviewed rewrite dashboard templates for customer, employee, and admin roles.
- Reviewed dashboard navigation and panel keys used by the shared dashboard
  JavaScript.
- Reviewed the existing parity checklist for viewport, role, and flow coverage.
- Did not execute an authenticated browser pass because this workspace does not
  provide seeded account credentials or a browser automation tool.

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

## Intentional Differences

- Old destructive delete flows remain represented by archive/deactivation
  behavior in the rewrite. This is intentional to preserve historical orders,
  reservations, feedback, notifications, and audit references.
- Completed order and reservation email summaries are sent in the email body
  rather than as separate text attachments because the current lightweight
  mailer supports body content only.

## Remaining Browser QA

Run the full checklist in `docs/dashboard-parity-qa.md` after seeded customer,
employee, and admin accounts are available. The browser pass still needs to
verify:

- Desktop, laptop, tablet, and mobile viewport rendering.
- Mouse, keyboard, and touch access to every dashboard section.
- No horizontal overflow in tables, filters, forms, modals, toasts, or fixed
  notifications.
- AJAX-enhanced mutations preserve the same visible state as fallback form
  submissions.
- Dashboard screenshots remain recognizably aligned with the legacy `grandego`
  dashboards.
