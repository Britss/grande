# Dashboard Browser Parity Run Sheet

Use this sheet when executing the authenticated visual parity pass described in
`docs/dashboard-parity-qa.md`. It is intentionally narrow: record only browser
observations that cannot be proven by the CLI smoke and viewport-readiness
scripts.

## Before Opening The Browser

Run the QA setup and contract checks with the XAMPP PHP binary:

```powershell
C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php
C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php
C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php
C:\xampp\php\php.exe scripts\dashboard_viewport_readiness_audit.php
```

Start the local app through Apache/XAMPP and open the rewrite routes:

- Customer: `/dashboard/customer`
- Employee: `/dashboard/employee`
- Admin: `/dashboard/admin`

Sign in with:

- Customer: `qa.customer@grande.local`
- Employee: `qa.employee@grande.local`
- Admin: `qa.admin@grande.local`

Password: `GrandeQA#2026`

## Viewport Matrix

Check each role at these widths:

| Role | 1440px | 1280px | 768px | 390px |
| --- | --- | --- | --- | --- |
| Customer | Not run | Not run | Not run | Not run |
| Employee | Not run | Not run | Not run | Not run |
| Admin | Not run | Not run | Not run | Not run |

Replace each `Not run` entry with `Pass`, `Pass with note`, or `Fail`.

## Screenshot Notes

Record screenshot filenames or paths beside the related viewport.

| Role | Viewport | Screenshot | Notes |
| --- | --- | --- | --- |
| Customer | 1440px |  |  |
| Customer | 1280px |  |  |
| Customer | 768px |  |  |
| Customer | 390px |  |  |
| Employee | 1440px |  |  |
| Employee | 1280px |  |  |
| Employee | 768px |  |  |
| Employee | 390px |  |  |
| Admin | 1440px |  |  |
| Admin | 1280px |  |  |
| Admin | 768px |  |  |
| Admin | 390px |  |  |

## Interaction Checks

For each dashboard role, verify:

- Every sidebar section is reachable by mouse.
- Every sidebar section is reachable with keyboard tab and enter/space.
- Mobile horizontal dashboard navigation remains scrollable and tappable.
- Detail modals open, scroll, and close without covering their close controls.
- Filter bars do not overflow or hide primary actions.
- AJAX-enhanced status/payment/feedback forms refresh the intended panel.
- Toasts and notification badges do not cover primary controls.
- Tables and report drilldowns remain horizontally scrollable instead of forcing
  page-level overflow.

## Browser Findings

Append browser-only defects here, then copy the confirmed findings into
`docs/dashboard-parity-qa-2026-04-14.md`.

| Finding | Role | Viewport | Severity | Status |
| --- | --- | --- | --- | --- |
|  |  |  |  |  |

## Completion Summary

- Browser and version:
- Date run:
- Tester:
- Overall result:
- Follow-up tasks opened:

## CLI Preflight Record

- Date run: 2026-04-14
- Commands:
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php`,
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php`,
  `C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php`,
  `C:\xampp\php\php.exe scripts\dashboard_viewport_readiness_audit.php`
- Result:
  Pass. QA accounts and workflow records were seeded, authenticated customer,
  employee, and admin dashboard contracts rendered, and viewport-readiness
  markers passed for all three dashboard shells.
- Browser status:
  Pending manual execution in an authenticated browser session; no browser
  automation dependency is present in this repository.

## Latest CLI Preflight Refresh

- Date run: 2026-04-14
- Commands:
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_accounts.php`,
  `C:\xampp\php\php.exe scripts\seed_dashboard_qa_workflow_data.php`,
  `C:\xampp\php\php.exe scripts\dashboard_parity_smoke.php`,
  `C:\xampp\php\php.exe scripts\dashboard_viewport_readiness_audit.php`
- Result:
  Pass. The workflow seed reported 2 QA menu items, 4 orders, 3 reservations,
  and 3 feedback records. Authenticated dashboard smoke checks and
  viewport-readiness markers passed for customer, employee, and admin.
- Browser status:
  Still pending manual execution in a browser session; this repository has no
  browser automation dependency or screenshot capture harness.
