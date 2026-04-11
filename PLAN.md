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
- Partially completed:
  dashboard UI parity. The main operational flows, first visual parity pass, queue filters, lightweight reporting graphs, customer feedback shortcuts, employee priority queue overview, admin attention queue, customer account-flow polish, customer cancellation controls, report export affordances, and shared dashboard density polish are implemented, but final browser-level visual QA against the old `grandego` dashboards can still tune any remaining per-module differences.
- Suggested next task:
  run browser-level dashboard visual QA next, comparing customer/admin/employee modules against the old `grandego` dashboards and tuning any remaining per-module spacing or density differences.

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
- Build admin dashboard:
  overview, orders, reservations, menu management, user management, feedback inbox, reports/graphs, audit trail.
  Remove duplicate widgets and keep one source of truth per metric.
- Current status:
  payment review, order management, reservation management, menu management, user management, feedback inbox, report summaries, audit log browsing, and detail snapshots are implemented.
  menu image upload is implemented with files saved under `public/uploads/menu-items` so XAMPP can serve them directly; order/reservation/menu/user filters, lightweight report graph visualizations, date-range reporting, and order/reservation drilldown tables are implemented.
  admin overview parity is expanded with a compact Needs Attention queue for pending payments, reservations, and feedback.
  admin dashboard density is tightened through shared cards, filters, report tables, and operational list styling.
  `grandego` should remain the visual and flow reference for dashboard parity, especially for admin panel layout, customer account flow, familiar menu/reservation interactions, filter placement, and chart/report presentation.
- Build employee dashboard:
  overview, orders queue, reservations queue.
  No user management, menu management, audit browsing, or admin reporting controls.
- Current status:
  payment review, order management, reservation management, feedback review, queue-oriented reports, and detail snapshots are implemented.
  employee sidebar styling, queue badges, compact cards, and mobile dashboard navigation now follow the old `grandego` staff dashboard more closely.
  employee-specific filtering and queue refinements now include dashboard filters plus a priority queue overview for payment, reservation, and feedback work.
  employee dashboard density is tightened through shared queue cards, filters, status blocks, and operational list styling.
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

## Test Plan
- Auth:
  signup, login, logout, inactive user block, role-based redirect, password reset.
- Current status:
  signup, login, logout, email verification, and role-based redirect are implemented and tested.
  inactive-user login blocking and password reset are implemented; reset links can be issued for inactive accounts, but inactive users remain blocked at login until reactivated.
- Public flow:
  menu browse, add to cart, checkout submission, reservation submission, feedback submission.
- Current status:
  menu browse, add to cart, checkout, reservation creation, reservation checkout, payment receipt upload, and payment review path are implemented.
  feedback submission and dashboard feedback history are implemented.
- Customer:
  can only see own orders/reservations/profile data; can cancel only eligible pending direct orders and eligible pending reservations.
- Current status:
  own orders/reservations are isolated correctly in the rebuilt dashboard.
  customers can update their own profile details with duplicate email/phone protection.
  customer profile guidance, dashboard password reset access, and actionable empty states are implemented.
  customer cancellation rules are implemented with ownership checks, pending-only guards, in-progress/terminal blocking, linked pending reservation-order cancellation, CSRF protection, and audit logging.
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

## Assumptions And Defaults
- Fresh database means no migration of current records; the rewrite will ship with a new schema and seedable admin account.
- The rewrite stays in plain PHP + MySQL for low operational complexity and XAMPP compatibility.
- Online ordering remains part of v1, including cart and checkout.
- Admin includes menu management in v1.
- Employee access is limited to orders and reservations only.
- Reservation workflow stays simple and production-safe; no in-app rollback path exists.
- Branding, page set, visual design, and core business intent from the current site are preserved.
- The rewrite is implementation-focused, not a redesign; any UI changes should be minimal and only made when required to support maintainability, responsiveness, accessibility, or correctness.
