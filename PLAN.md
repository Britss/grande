# GrandeGo Ground-Up Rewrite Plan

## Summary
Rewrite GrandeGo as a clean PHP + MySQL modular monolith for XAMPP with a fresh database, keeping the full customer ordering flow plus reservations, feedback, and role-based dashboards. The rewrite must preserve design parity with the current `grandego` website: same overall visual design, branding, page structure, user flow, and familiar interface patterns, while replacing the underlying implementation with a cleaner architecture. The new codebase will be structured so views only render data, controllers handle requests, services own business rules, repositories own SQL, and all status changes pass through one workflow layer with audit logging.

## Progress Snapshot
- Completed:
  front controller/router bootstrap, shared layout, public page rewrite foundation, self-contained local assets, and design-parity-oriented public styling.
- Completed:
  auth foundation with login, logout, signup, CSRF, validation, sessions, role redirects, and pre-account email verification.
- Completed:
  `grande` database setup for users, menu catalog, cart, orders, order items, reservations, and signup verification.
- Completed:
  imported menu catalog from `grandego_db` into `grande.menu_items` and `grande.menu_item_sizes`, and `/menu` now reads from the rebuilt `grande` database.
- Completed:
  public menu search and category filtering parity has been restored from the old `grandego` menu page. `/menu` now includes a search input, category tabs, item/category data hooks, hidden empty category sections, and a no-results state without adding a new backend endpoint.
- Completed:
  shared-cart flow aligned with old `grandego` behavior:
  `menu -> cart -> checkout` for direct orders, and `menu -> cart -> reserve -> reservation-checkout` for reservation-linked orders.
- Completed:
  checkout payment step with GCash QR display, receipt upload, receipt storage, and `orders.receipt_image` / `payment_status` support.
- Completed:
  customer dashboard now shows real order history and reservation history with item-level/order-linked detail snapshots.
- Completed:
  admin and employee dashboards now support payment review, order status management, reservation status management, and detailed order/reservation snapshot views.
- Completed:
  back-office menu management, admin user management, feedback inbox management, report summaries, and audit log storage are now wired into the rebuilt dashboards.
- Completed:
  staff dashboard mutation forms now progressively enhance to AJAX panel refreshes while keeping form-post + redirect behavior as the non-JavaScript fallback.
- Completed:
  customer profile editing is now wired through the customer dashboard with validation, duplicate email/phone checks, session refresh, and audit logging.
- Completed:
  admin menu management now accepts JPG/PNG/WEBP image uploads for menu items, saves display-ready paths on `menu_items.image_url`, and renders uploaded menu images on the public menu.
- Completed:
  dashboard UI parity pass using `grandego` as direct inspiration: customer sidebar flow now follows Dashboard/Profile/Reservations/Orders, staff dashboards use the darker operational sidebar treatment, sidebar queue badges show pending work, dashboard cards are more compact, and mobile dashboard navigation behaves like the old horizontally scrollable menu.
- Completed:
  richer staff dashboard filtering and lightweight report graphs using `grandego` as direct inspiration: order and reservation queues now have search/status/flow/payment-readiness filters, reports now include last-7-day sales, order volume, reservation volume, and top-selling menu item bar summaries without adding external chart dependencies.
- Completed:
  admin menu and user management filters now match the compact dashboard filtering pattern: menu management supports search plus live/archive visibility filtering, and user management supports search plus role/status filtering.
- Completed:
  password reset is implemented with request/reset routes, `password_resets` migration, hashed reset tokens, XAMPP-friendly local mail preview links, password validation, consumed-token handling, and audit logging for reset request/completion.
- Completed:
  fuller admin report drilldowns are implemented: report date-range controls, selected-range summary cards, daily sales/order/reservation series generated from repository-owned SQL, top-item range filtering, and export-friendly order/reservation detail tables.
- Completed:
  customer cancellation rules are implemented: customers can cancel only their own pending direct orders and pending reservations without in-progress linked orders, terminal/in-progress records are blocked, linked pending reservation orders are cancelled with the reservation, and each successful customer cancellation writes an audit log.
- Completed:
  deeper dashboard parity details are expanded: customer dashboards now include a feedback section, feedback sidebar/stat shortcuts, latest-feedback overview cards, and recent feedback history; employee dashboards now include a priority queue snapshot for pending payment review, pending reservations, and new feedback.
- Completed:
  admin overview micro-interactions now include a Needs Attention queue that jumps directly to pending payment review, pending reservations, and new feedback using the same compact dashboard pattern as the employee workspace.
- Completed:
  customer account-flow polish is implemented: profile editing now includes clearer dashboard-side guidance, password reset access is linked from the customer profile panel, and empty order/reservation/feedback states now point customers to the next useful action.
- Completed:
  follow-up dashboard parity polish is implemented: customer account panels now include a profile readiness checklist, and admin reports include print/save-as-PDF controls plus clearer export guidance beside the drilldown tables.
- Completed:
  dashboard density polish is implemented: shared dashboard spacing, hero height, statistic cards, records panels, operational cards, order line items, and filter bars are tightened to better match the older compact `grandego` dashboard rhythm without changing workflows.
- Completed:
  public assistant widget parity is implemented on non-dashboard pages: floating coffee button, unread badge, first-open welcome sequence, timestamped bot/user bubbles, typing indicator, quick replies, rewrite route links, escaped typed messages, keyboard close behavior, and mobile-aware styling.
- Completed:
  customer reorder parity is implemented from the dashboard: customers can add a previous order back to their cart, unavailable archived menu items/sizes are skipped, current catalog prices are used, CSRF is enforced, and successful reorder actions are audit logged.
- Completed:
  authenticated customer change-password parity is implemented from the profile dashboard: customers verify their current password, submit a policy-compliant new password, receive CSRF protection and validation feedback, keep reset-link access as fallback, and successful changes are audit logged.
- Completed:
  customer profile-picture upload parity is implemented from the profile dashboard: customers can upload JPG/PNG/WEBP images, files are saved under `public/uploads/profile-pictures`, `users.profile_picture` is refreshed, the session is updated, old uploaded profile pictures are cleaned up, and successful changes are audit logged.
- Completed:
  customer email updates for staff/admin order and reservation status changes are implemented as non-blocking status notifications using the existing local-mail/SMTP-friendly mailer path. Orders email customers when moved to `ready`, `completed`, `cancelled`, or `rejected`; reservations email customers when moved to `confirmed`, `completed`, or `cancelled`; completed messages include text receipt/reservation summaries in the email body, and mail failures are logged without rolling back the status update.
- Completed:
  customer dashboard order/reservation notifications are implemented with versioned notification tables, repository-owned create/read/mark-read logic, staff/admin status-change hooks, a customer JSON polling route, dashboard toast display, and a compact latest-updates feed.
- Completed:
  staff new-order polling parity is implemented for admin and employee dashboards with role-protected polling routes, repository-owned order summary counts, sidebar badge refreshes, and a compact toast that links staff to payment review or order management when new orders arrive after the dashboard opens.
- Completed:
  authenticated staff/admin change-password parity is implemented from the staff dashboards: admin and employee users now have an Account panel, verify their current password, submit a policy-compliant new password, receive CSRF protection and dashboard AJAX/fallback handling, keep reset-link access as fallback, and successful changes are audit logged.
- Completed:
  menu JSON getter compatibility is implemented as a read-only bridge: `/api/menu-items` and old-style `/get_menu_items.php` both return active catalog items from `MenuRepository`, including normalized size data, without reintroducing page-level SQL handlers.
- Completed:
  remaining read-only standalone JSON getter compatibility bridges are implemented for old dashboard/public consumers: `/get_orders.php`, `/get_order_items.php`, `/get_reservation_orders.php`, `/get_customer_reservation_orders.php`, `/get_feedback.php`, and `/get_customers.php`, with clean `/api/...` route aliases. These endpoints are role-protected, repository-owned, and preserve old response envelopes where practical.
- Completed:
  old report chart/report getter compatibility bridges are implemented for admin-only read consumers: `/includes/handlers/reports/get_sales_chart_data.php` and `/includes/handlers/reports/get_sales_report.php`, with clean `/api/reports/sales-chart-data` and `/api/reports/sales-report` aliases. These endpoints are backed by `ReportRepository`, preserve the old Chart.js/report response shapes, and avoid reintroducing page-level SQL handlers.
- Completed:
  the remaining customer hard-delete parity decision is resolved: the old customer delete handler is intentionally represented as customer deactivation, preserving historical orders, reservations, feedback, notifications, and audit references.
- Completed:
  browser-level dashboard comparison has been made repeatable through `docs/dashboard-parity-qa.md`, covering legacy dashboard reference files, rewrite routes, desktop/mobile viewport checks, shared dashboard expectations, role-specific customer/employee/admin parity points, and pass criteria for documenting intentional visual differences.
- Completed:
  dashboard parity QA documentation now includes a dated static run record in `docs/dashboard-parity-qa-2026-04-14.md`, covering customer, employee, admin, shared dashboard behavior, intentional differences, and the exact browser checks that still require seeded accounts.
- Completed:
  repeatable dashboard QA account seeding is implemented through `scripts/seed_dashboard_qa_accounts.php`, documenting active customer, employee, and admin credentials for the authenticated browser parity checklist without creating workflow data.
- Completed:
  dashboard QA accounts were seeded locally on 2026-04-14 using the XAMPP PHP binary, and the QA documentation now records the direct `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php` fallback for shells where `php` is not on PATH.
- Completed:
  repeatable non-empty dashboard QA workflow data seeding is implemented through `scripts/seed_dashboard_qa_workflow_data.php`, creating QA-only menu items, direct orders, a reservation-linked order, reservations, and feedback records for authenticated customer/employee/admin browser parity checks. The workflow seed was run locally on 2026-04-14 with the XAMPP PHP binary.
- Completed:
  authenticated dashboard parity smoke testing is implemented through `scripts/dashboard_parity_smoke.php`, rendering the seeded customer, employee, and admin dashboards in CLI session context and checking role-specific navigation/account markers plus forbidden panel/public assistant exclusions. The smoke test was run locally on 2026-04-14 with the XAMPP PHP binary.
- Completed:
  authenticated dashboard viewport-readiness auditing is implemented through `scripts/dashboard_viewport_readiness_audit.php`, rendering the seeded customer, employee, and admin dashboards in CLI session context and checking role-specific target/panel wiring plus responsive CSS markers for mobile dashboard navigation, horizontally safe report tables, modal height limits, compact filters, and dashboard assistant exclusion. The audit was run locally on 2026-04-14 with the XAMPP PHP binary.
- Completed:
  browser dashboard parity QA handoff is now documented through `docs/dashboard-browser-parity-run-sheet.md`, giving the manual authenticated browser pass a role/viewport matrix, screenshot note table, interaction checks, defect log, and completion summary tied back to the dated QA run record.
- Suggested next task:
  execute the authenticated browser parity QA checklist against the seeded customer, employee, and admin accounts plus seeded workflow records, using `docs/dashboard-browser-parity-run-sheet.md` to capture viewport-specific defects and screenshot notes before appending confirmed findings to `docs/dashboard-parity-qa-2026-04-14.md`.

## Direct GrandeGo Parity Audit
- Source checked:
  `c:\xampp\htdocs\grandego` was compared against this rewrite, including old page files, public JavaScript, dashboard JavaScript, helper functions, and `includes/handlers` backend entrypoints.
- Completed parity:
  core public routes, auth, cart, direct checkout, reservation checkout, receipt upload, payment review, order status management, reservation status management, feedback submission/review, menu item/size management, user activation/deactivation through `is_active`, reports, customer profile editing, customer cancellation rules, and dashboard filtering for staff/admin work queues.
- Recently fixed parity:
  old public menu search/category filtering from `grandego/pages/menu.php` and `grandego/assets/js/pages/public/menu.js` is now implemented in the new `/menu` page.
- Completed parity:
  the public assistant widget from `includes/components/chatbot-widget.php`, `assets/css/components/assistant-widget.css`, and `assets/js/components/assistant-widget.js` has been ported into the rewrite as a non-dashboard partial with safer delegated JavaScript behavior and rewrite URLs.
- Completed parity:
  customer reorder flows from `orders/reorder_order.php` and `orders/reorder_latest.php` are now represented by a dashboard reorder action that rebuilds the customer cart from previous order items that still exist in the active catalog.
- Completed parity:
  customer order/reservation notification tables and the old unread-notification polling behavior are represented by `customer_order_notifications`, `customer_reservation_notifications`, `CustomerNotificationRepository`, `/dashboard/customer/notifications`, a dashboard toast, and a compact latest-updates feed. Notifications are marked read after the polling route returns them, matching the old handler behavior.
- Completed parity:
  customer email updates for staff/admin order and reservation status changes from `orders/update_order_status.php` and `reservations/update_reservation_status.php` are represented by `StatusNotificationService`. The rewrite sends non-blocking customer emails for orders moved to `ready`, `completed`, `cancelled`, or `rejected`, and for reservations moved to `confirmed`, `completed`, or `cancelled`. Completed summaries are included in the email body instead of separate text attachments because the current lightweight mailer supports plain/html bodies only.
- Completed parity:
  staff new-order polling from `orders/check-new-orders.php` is represented by admin/employee dashboard polling routes that watch for orders created after the current dashboard snapshot, refresh queue badges, and show staff a toast linking to payment review or order management.
- Completed parity:
  old menu list getter compatibility is represented by `/get_menu_items.php`, with `/api/menu-items` as the clean rewrite route. Both routes return live menu items and available sizes from the rebuilt `grande` catalog through repository-owned reads.
- Completed parity:
  old read-only dashboard JSON getters are represented by compatibility routes for orders, order items, reservation orders, customer reservation orders, feedback, and customers. Each route has an old filename bridge plus a clean `/api/...` alias, uses the rewrite repositories for SQL ownership, and enforces the same broad role boundaries as the old handlers.
- Completed parity:
  old report chart/report JSON getters from `includes/handlers/reports/get_sales_chart_data.php` and `includes/handlers/reports/get_sales_report.php` are represented by admin-only compatibility routes plus clean `/api/reports/...` aliases, returning Chart.js-friendly sales/category payloads and date-range report summaries from repository-owned SQL.
- Completed parity:
  customer profile-picture upload from `uploads/upload-profile-picture.php` is now represented by the customer profile dashboard upload form, backed by `users.profile_picture` and public uploaded image rendering in the dashboard sidebar/profile panel.
- Completed parity:
  authenticated dashboard password change from `auth/change-password.php` is now implemented for customer profile dashboards and staff/admin Account panels, with current-password verification, password policy validation, CSRF protection, reset-link fallback, and audit logging.
- Remaining parity gaps:
  old menu delete behavior from `menu/delete_menu_item.php` is intentionally represented as availability/archive-style management rather than hard delete, preserving historical order/audit references.
- Completed parity:
  old customer hard-delete behavior from `includes/handlers/customers/delete_customer.php` is intentionally bridged to account deactivation through `is_active = 0`, with cart cleanup and audit logging. The clean alias is `/api/customers/deactivate`; historical customer-linked records are preserved instead of deleted.
- Remaining visual QA:
  execute the repeatable authenticated browser checklist in `docs/dashboard-parity-qa.md` against old `grandego` and this rewrite, then append viewport-specific defects or screenshot notes to `docs/dashboard-parity-qa-2026-04-14.md`. The static review portion is documented there already.

## Assistant Widget Implementation Plan
- Current status:
  completed in the rewrite using `app/Views/partials/assistant-widget.php`, explicit public/customer route inclusion in `app/Views/layouts/app.php`, delegated JavaScript in `public/assets/js/app.js`, and widget styles in `public/assets/css/app.css`.
- Goal:
  restore the old public-page `GrandeGo. Assistant` parity as a lightweight, static FAQ assistant before final dashboard visual QA. This should not introduce external AI/API dependencies for v1.
- Old behavior to match:
  floating coffee-icon button, unread badge, compact chat window, welcome messages on first open, close button, typing indicator, timestamped bot/user bubbles, quick-reply buttons, keyword-based answers, and links to menu/reservation/feedback/contact flows.
- Scope for v1:
  implement the assistant on public pages only: home, about, menu, reserve, feedback, cart, checkout, and reservation checkout. Keep dashboards focused on operational work unless a later parity pass confirms the old dashboards included the widget there.
- Files to add/update:
  add `app/Views/partials/assistant-widget.php` for reusable markup.
  update `app/Views/layouts/app.php` to include the widget for non-dashboard pages.
  add assistant styles into `public/assets/css/app.css` or a dedicated `public/assets/css/assistant-widget.css` if the asset layout is split later.
  extend `public/assets/js/app.js` with a namespaced assistant initializer, or add `public/assets/js/assistant-widget.js` if keeping it separate is cleaner.
- Route/link adaptation:
  convert old hardcoded `pages/menu.php` and `pages/reserve.php` links to rewrite URLs via `url('menu')`, `url('reserve')`, `url('feedback')`, and the current contact details from shared public content.
  expose a small `window.GRANDE_ASSISTANT` config object from the layout or partial so JavaScript does not hardcode paths.
- Implementation steps:
  1. Port the old widget markup into a partial using the rewrite helpers `url()`, `asset()`, and `e()`.
  2. Add layout inclusion rules so the widget appears on public/customer-facing pages and is excluded from dashboard pages.
  3. Port the JavaScript behavior while removing inline `onclick`; bind quick replies with delegated event listeners and escape user input before rendering.
  4. Port the visual style into the current design system, preserving old placement and feel while avoiding conflicts with the mobile nav, cart actions, receipt upload, and modal layers.
  5. Update answer copy for rewrite routes and current flows: menu browsing, cart/checkout, reservation, GCash/cash payment, contact, 24/7 hours, and location.
  6. Add accessibility polish: real buttons, `aria-expanded`, `aria-hidden`, close on Escape, focus the input on open, and keep focus behavior predictable on mobile.
  7. Verify desktop and mobile rendering on all public pages, especially menu/cart/checkout where fixed-position controls can overlap.
- Acceptance criteria:
  the assistant button is visible and non-overlapping on public pages.
  first open shows the welcome sequence and hides the badge.
  quick replies produce the expected answers without inline JavaScript.
  typed user messages are escaped and cannot inject HTML.
  response links navigate to the rewrite routes.
  the widget is absent from admin/employee/customer dashboards unless intentionally enabled later.
  mobile view keeps the input, close button, and quick replies usable without covering checkout controls.
- Deferred decisions:
  whether to persist chat history in localStorage.
  whether to add backend-managed FAQ content.
  whether to add staff/admin live chat or an AI-backed assistant.
  whether the assistant should appear for logged-in dashboard customers after the main parity gaps are complete.

## Implementation Changes
- Use a simple MVC-style structure:
  `public/` for entrypoints and assets, `app/Controllers`, `app/Services`, `app/Repositories`, `app/Views`, `app/Middleware`, `app/Support`, `database/migrations`, `storage/uploads`.
- Preserve front-end design parity with the current `grandego` site:
  keep the same look and feel, page sections, navigation patterns, content hierarchy, and recognizable user experience unless a small adjustment is required for technical correctness or responsive stability.
- Add a single bootstrap/config flow:
  environment config, DB connection, session bootstrap, auth helpers, CSRF protection, validation helpers, and centralized redirect/response utilities.
- Replace page-level SQL with service/repository modules:
  `Auth`, `Menu`, `Cart`, `Checkout`, `Orders`, `Reservations`, `Feedback`, `Users`, `Reports`, `AuditTrail`.
- Build public pages:
  landing, about, menu, reservation, feedback, login, signup.
- Current status:
  landing, about, menu, reservation, feedback, login, signup, and email verification are rebuilt and routing through the new app structure.
  public menu search and category filtering now match old `grandego` behavior client-side while using the new repository-rendered catalog.
  feedback submissions now persist to `grande.feedback`.
- Build customer dashboard:
  overview, active orders, order history, reservation history, profile/account, feedback shortcut.
  Show only actionable customer information: latest order state, latest reservation state, totals, account status.
- Current status:
  overview, order history, reservation history, and detail snapshots are implemented.
  profile editing is implemented with dashboard form submission, customer-only persistence, and audit logging.
  customer sidebar flow, work badges, compact cards, and mobile dashboard navigation now follow the old `grandego` dashboard more closely.
  feedback shortcut parity is expanded with a dedicated dashboard section, sidebar/stat entry points, latest-feedback summary, and recent feedback history.
  account-flow polish now adds profile guidance, password reset access, and clearer action-oriented empty states for orders, reservations, and feedback.
  shared dashboard density styles now make customer records, summary cards, and dashboard panels more compact and closer to the old dashboard rhythm.
- Current status:
  customer dashboard status notifications are implemented for order/reservation status changes, and staff-side new-order polling is implemented for admin/employee dashboards.
- Build admin dashboard:
  overview, orders, reservations, menu management, user management, feedback inbox, reports/graphs, audit trail.
  Remove duplicate widgets and keep one source of truth per metric.
- Current status:
  payment review, order management, reservation management, menu management, user management, feedback inbox, report summaries, audit log browsing, and detail snapshots are implemented.
  menu image upload is implemented with files saved under `public/uploads/menu-items` so XAMPP can serve them directly; order/reservation/menu/user filters, lightweight report graph visualizations, date-range reporting, and order/reservation drilldown tables are implemented.
  admin self-service password changes are implemented from a dedicated Account panel with current-password verification and audit logging.
  admin overview parity is expanded with a compact Needs Attention queue for pending payments, reservations, and feedback.
  admin dashboard density is tightened through shared cards, filters, report tables, and operational list styling.
  `grandego` should remain the visual and flow reference for dashboard parity, especially for admin panel layout, customer account flow, familiar menu/reservation interactions, filter placement, and chart/report presentation.
- Current gap:
  old hard-delete endpoints for menu/customer records and old standalone JSON list/detail endpoints are not cloned. The rewrite currently prefers safer status/archive updates and rendered dashboard data.
- Build employee dashboard:
  overview, orders queue, reservations queue.
  No user management, menu management, audit browsing, or admin reporting controls.
- Current status:
  payment review, order management, reservation management, feedback review, queue-oriented reports, and detail snapshots are implemented.
  employee self-service password changes are implemented from a dedicated Account panel with current-password verification and audit logging.
  employee sidebar styling, queue badges, compact cards, and mobile dashboard navigation now follow the old `grandego` staff dashboard more closely.
  employee-specific filtering and queue refinements now include dashboard filters plus a priority queue overview for payment, reservation, and feedback work.
  employee dashboard density is tightened through shared queue cards, filters, status blocks, and operational list styling.
- Current status:
  old employee new-order polling/notification behavior is implemented with dashboard polling, badge refreshes, and staff queue toasts.
- Current status:
  old read-only dashboard getter endpoints for orders, order details, reservation-linked orders, customer reservation-linked orders, feedback, and customers are implemented as compatibility bridges with clean `/api/...` aliases.
- Define strict role permissions:
  customer can manage own profile, orders, reservations, feedback;
  employee can process payments, orders, reservations, and feedback queues only;
  admin can manage all modules including users, menu, reports, audit.
- Define forward-only workflows:
  Order status: `pending -> preparing -> ready -> completed`, with terminal `cancelled` and `rejected`.
  Reservation status: `pending -> confirmed -> completed`, with terminal `cancelled`.
  Terminal states are immutable in-app. Backward movement is not supported.
- Keep payment verification as a separate field for orders:
  `payment_status = pending | verified | rejected`.
  Orders cannot move from `pending` to `preparing` unless payment is `verified`.
- Current status:
  implemented, including reservation lock checks that require all linked reservation orders to be payment-verified before reservation status can move forward.
- Add audit trail for every operational mutation:
  login, signup, user activation/deactivation, menu create/update/archive, order status change, reservation status change, feedback status update.
  Store actor, role, entity type, entity id, action, before snapshot, after snapshot, timestamp.
- Current status:
  operational dashboard mutations now write audit rows for payments, orders, reservations, menu updates, user updates, and feedback status changes.
- Use soft operational controls where needed:
  users and menu items should be archived/deactivated instead of hard-deleted from dashboards.
  Keep destructive deletes out of normal production flows.
- Replace runtime schema mutations with versioned SQL migrations only.
  No `CREATE TABLE` or `ALTER TABLE` logic inside request-time PHP code.

## Public APIs / Data Contracts
- Routes should be explicit and grouped by responsibility:
  public pages, auth actions, customer actions, admin actions, employee actions.
- Request handlers should return:
  HTML for page routes, JSON for dashboard actions/modals/tables.
- Current status:
  public pages and customer operations use HTML form posts plus redirects.
  staff dashboard operations return JSON panel markup for AJAX requests and keep redirects as the fallback.
  detail snapshots are rendered client-side from embedded payloads, not fetched by JSON endpoints yet.
  menu catalog compatibility JSON is available at `/api/menu-items` and `/get_menu_items.php`.
  read-only compatibility JSON is also available for orders, order items, reservation orders, customer reservation orders, feedback, and customers through old filename routes and clean `/api/...` aliases.
- Compatibility note:
  this differs from old `grandego`, which exposed many standalone JSON handler files for dashboard lists, item details, notifications, chart data, and mutations. Add compatibility routes only if old JavaScript/API compatibility is a project requirement.
- Core tables for v1:
  `users`, `menu_items`, `menu_item_sizes`, `cart_items`, `orders`, `order_items`, `reservations`, `feedback`, `audit_logs`, `password_resets`.
- Key schema rules:
  `users.role = customer | admin | employee`
  `users.is_active` boolean
  `menu_items.is_active` boolean
  `orders.order_number` unique
  `audit_logs` append-only
- Menu pricing model:
  keep size-based pricing, but normalize it cleanly as one menu item with one-or-many price rows instead of mixed fallback logic.
- Dashboard metrics:
  admin graphs should include daily sales, order volume by day, reservation volume by day, and top-selling menu items.
  employee dashboard should use queue metrics only, not business analytics.
- Current status:
  admin reports render selectable date-range daily sales, order volume, reservation volume, top-selling menu item bars, selected-range summary cards, and order/reservation drilldown tables from repository-owned SQL.
  legacy admin report chart/report JSON consumers can read `/includes/handlers/reports/get_sales_chart_data.php` and `/includes/handlers/reports/get_sales_report.php`, or the clean `/api/reports/sales-chart-data` and `/api/reports/sales-report` aliases.

## Test Plan
- Auth:
  signup, login, logout, inactive user block, role-based redirect, password reset.
- Current status:
  signup, login, logout, email verification, and role-based redirect are implemented and tested.
  inactive-user login blocking and password reset are implemented; reset links can be issued for inactive accounts, but inactive users remain blocked at login until reactivated.
- Public flow:
  menu browse, add to cart, checkout submission, reservation submission, feedback submission.
- Current status:
  menu browse, search, category filtering, add to cart, checkout, reservation creation, reservation checkout, payment receipt upload, and payment review path are implemented.
  feedback submission and dashboard feedback history are implemented.
- Customer:
  can only see own orders/reservations/profile data; can cancel only eligible pending direct orders and eligible pending reservations; can reorder previous orders back into the cart when catalog items/sizes are still available.
- Current status:
  own orders/reservations are isolated correctly in the rebuilt dashboard.
  customers can update their own profile details with duplicate email/phone protection.
  customers can change their password from the profile dashboard after verifying the current password; successful changes are audit logged.
  customer profile guidance, dashboard password reset access, and actionable empty states are implemented.
  customer cancellation rules are implemented with ownership checks, pending-only guards, in-progress/terminal blocking, linked pending reservation-order cancellation, CSRF protection, and audit logging.
  customer reorder is implemented with ownership checks, CSRF protection, current catalog pricing, unavailable item/size skipping, cart merge behavior, cart redirect, and audit logging.
- Admin:
  can manage users/menu/orders/reservations/feedback; every mutation writes an audit log row.
- Current status:
  admin can manage order payments, order statuses, reservations, menu items/sizes, users, and feedback.
  dashboard mutations write audit records; menu image upload and richer management filters are implemented.
- Employee:
  can process orders/reservations only; access to admin-only modules is denied.
- Current status:
  employee can process payments, orders, reservations, and feedback in the rebuilt dashboard flow.
- Workflow enforcement:
  backward status changes fail; terminal states cannot be reopened; unpaid orders cannot enter preparation.
- Current status:
  unpaid-order and reservation-payment locks are enforced.
  finalized order/reservation edits are blocked.
- Data integrity:
  archived users/menu items remain historically visible in past orders/audit logs.
- UI regression:
  public pages and all dashboards render correctly on desktop and mobile with no duplicate or conflicting data blocks.
- GrandeGo parity regression:
  compare against `c:\xampp\htdocs\grandego` for old handler-backed flows, especially menu search/filter, dashboard notifications, order/reservation status email updates, profile-picture upload, change password, live order polling, menu/customer delete behavior, and dashboard JSON endpoint expectations.
  verify customer reorder from the latest-order card and order-history cards adds available historical items to the cart, skips archived menu items/sizes with a clear flash message, uses current prices, and never allows reordering another customer's order.
  verify that staff/admin order status changes send non-blocking customer emails for `ready`, `completed`, `cancelled`, and `rejected`, with completed orders including a receipt-summary text section.
  verify that staff/admin reservation status changes send non-blocking customer emails for `confirmed`, `completed`, and `cancelled`, with completed reservations including a reservation-summary text section.
  verify mail failures are logged without rolling back the already-persisted status update.

## Assumptions And Defaults
- Fresh database means no migration of current records; the rewrite will ship with a new schema and seedable admin account.
- The rewrite stays in plain PHP + MySQL for low operational complexity and XAMPP compatibility.
- Online ordering remains part of v1, including cart and checkout.
- Admin includes menu management in v1.
- Employee access is limited to orders and reservations only.
- Reservation workflow stays simple and production-safe; no in-app rollback path exists.
- Branding, page set, visual design, and core business intent from the current site are preserved.
- The rewrite is implementation-focused, not a redesign; any UI changes should be minimal and only made when required to support maintainability, responsiveness, accessibility, or correctness.
