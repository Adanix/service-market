# ServiceLocal - Local Services Marketplace (Republic of Ireland)

ServiceLocal is a location-based services marketplace demo built for the Republic of Ireland. It provides a complete three-sided workflow connecting Customers, Service Providers, and Platform Administrators without third-party external dependencies, real payment credentials, or password forms.

All UI, code, seed data, and documentation are strictly written in English. The application uses integer cents in EUR for all monetary calculations and strictly avoids emoji.

---

## 1. Technology Stack

- **Backend Framework**: Laravel 13.x (PHP 8.3+)
- **Database**: SQLite with foreign key enforcement and WAL mode
- **Frontend Architecture**: Blade Components, Tailwind CSS v4, Alpine.js v3
- **Icons**: Lucide SVG inline icons (strictly zero emoji)
- **Testing Framework**: Pest PHP v4 with Unit and Feature test suites
- **Development & Runtime Environment**: Adanix Docker containers (`service-market-app-1`)

---

## 2. Seeded Demo Accounts (Passwordless Authentication)

Access any persona by visiting `/demo/sign-in` or using direct links:

| Name | Role | Email | Profile / Scope |
| --- | --- | --- | --- |
| Aoife Murphy | Administrator | aoife.admin@demo.test | Marketplace oversight, moderation, payouts, settings, audit log |
| Liam O'Connor | Provider | liam.provider@demo.test | O'Connor Home Cleaning (Dublin). Approved provider with 4 services |
| Maria Silva | Provider | maria.provider@demo.test | Silva Garden Care (Cork). Approved provider with 4 services |
| Tomas Novak | Provider | tomas.provider@demo.test | Novak Plumbing (Galway). Pending provider status |
| Sinead Byrne | Provider | sinead.provider@demo.test | Byrne Repair (Limerick). Suspended provider account |
| Emma Walsh | Customer | emma.customer@demo.test | Dublin customer with active and past bookings |
| Jack Ryan | Customer | jack.customer@demo.test | Cork customer with active and completed bookings |

---

## 3. Simulated Payment Card Matrix

The checkout simulation operates entirely in application code without external payment gateway SDKs or real API credentials. Full card numbers, expiration dates, and CVC codes are never persisted to the database.

| Card Number | Brand | Simulated Behavior / Outcome |
| --- | --- | --- |
| 4242 4242 4242 4242 | Visa | Approved immediately. Booking placed into Pending Provider state. |
| 5555 5555 5555 4444 | Mastercard | Approved immediately. Booking placed into Pending Provider state. |
| 4000 0000 0000 0002 | Visa | Declined with message: Card declined by issuer. |
| 4000 0000 0000 0051 | Visa | Declined with message: Insufficient funds. |
| 4000 0000 0000 0019 | Visa | Declined with message: Transaction temporarily blocked. |
| Any other card | - | Declined with message: Unrecognized demo card number. |

---

## 4. Key Operational and Console Commands

Run these commands inside the application container or environment:

```bash
# Run the complete automated test suite
php artisan test

# Reset the demo environment to its pristine seeded state
php artisan demo:reset --force

# Fast-forward demo time by one day and trigger scheduled jobs
php artisan demo:tick

# Run individual background scheduled tasks
php artisan bookings:expire-authorizations
php artisan bookings:auto-complete
php artisan bookings:send-reminders

# Clear and cache application views
php artisan view:clear
php artisan view:cache

# Rebuild frontend assets (run from host)
/opt/adanix/bin/adanix build service-market
```

---

## 5. Architectural & Design Decisions

- **Integer Cents in EUR**: All monetary amounts are stored as integer cents (`price_cents`, `amount_cents`, `fee_cents`, `net_cents`). Formatting produces standard Irish euro strings (e.g., `€85.00`) using the dedicated `App\Support\Money` value object. Floating point math is never used.
- **Two-Phase Payment Escrow**: Checkout authorizes a hold on customer funds without capturing immediately. Capture occurs only upon customer confirmation or automated completion after the dispute window expires.
- **State Machine Integrity**: Bookings transition through explicit states (`PendingProvider`, `Accepted`, `InProgress`, `AwaitingConfirmation`, `Completed`, `Declined`, `Cancelled`, `Expired`) enforced by `App\Enums\BookingStatus` and guarded by authorization policies.
- **Security & Privacy**: Strict role middleware (`role:customer`, `role:provider`, `role:admin`) isolates tenant workspaces. Idempotency keys prevent duplicate payments on repeated checkout submissions.
- **Accessibility & Hygiene**: Pure SVG icons, semantic HTML, responsive layout down to 320px width, zero emoji, zero Indonesian terms, zero hardcoded credentials.

---

## 6. Documentation

A comprehensive in-app documentation page is available at `/docs`, containing all twelve required architectural, operational, and regulatory sections for evaluation and review.
