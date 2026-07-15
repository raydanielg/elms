# ELMS — Advanced Features, Dynamic Engine & Integrations
## Part 2: Extended System Architecture Document

**Core Technology:** Laravel (PHP Framework)
**Document Version:** 1.0 — Companion to "ELMS_Architecture.md"
**Focus:** Making the system fully dynamic, configurable without code changes, and deeply integrated with Payment Gateways + SMS providers.

---

## TABLE OF CONTENTS

1. Introduction — Why "Dynamic" Matters
2. Dynamic System Configuration Engine
3. Payment Gateway Integration Architecture (Deep Dive)
4. SMS Integration Architecture (Deep Dive)
5. Dynamic Course Builder Enhancements
6. Dynamic Forms & Custom Fields Engine
7. Workflow Automation & Rules Engine
8. Plugin/Extension System (App Marketplace for the LMS itself)
9. Multi-Currency & Multi-Region Support
10. Dynamic Theming & White-Label Branding
11. Advanced Custom Report Builder
12. Webhooks & Developer API
13. Advanced Notification Engine (Templates + Triggers)
14. Dynamic Pricing Engine
15. Content Versioning & Approval Workflows
16. AI-Enhanced Features
17. Advanced Marketplace Growth Features
18. Mobile & Offline-First Enhancements
19. Advanced Security Add-ons
20. Scalability Path Toward Microservices
21. Conclusion

---

## 1. INTRODUCTION — WHY "DYNAMIC" MATTERS

A truly dynamic LMS means the **Super Admin and Admins can change how the system behaves without a developer touching code**. Instead of hardcoding plan limits, payment providers, SMS providers, quiz types, certificate designs, or pricing rules, everything is stored as **configuration data in the database** and controlled through admin-facing settings screens.

This document expands the original architecture with the mechanisms that make ELMS dynamic, flexible, and deeply integrated with real-world payment and messaging infrastructure — especially for the East African market.

---

## 2. DYNAMIC SYSTEM CONFIGURATION ENGINE

### 2.1 Settings-as-Data
Instead of `.env`-only configuration, ELMS maintains a `system_settings` table (key-value + JSON) that powers:
- Branding (platform name, logo, colors, favicon) — global and per-tenant (white-label).
- Feature toggles (enable/disable Live Classes, Forums, Certificates, Gamification per plan).
- Commission rates per plan tier (can be changed by Super Admin without a deployment).
- Default currency, supported currencies, and exchange rate source.
- Default language and list of enabled languages.
- Email/SMS sender identities per tenant (custom "from" name).

### 2.2 Feature Flags Engine
- Every module (Quizzes, Live Classes, Forums, Gamification, Certificates, Affiliate Program) is wrapped behind a **Feature Flag**.
- Feature flags are assigned at three levels: **Global** (platform-wide), **Plan-level** (only available on certain subscription tiers), and **Tenant-level override** (Super Admin can manually grant/revoke a feature for a specific tenant, e.g., as a promotional trial).
- Flags are cached (Redis) and checked via a simple `feature('live_classes')->isEnabledFor($tenant)` style helper, so enabling/disabling a feature platform-wide takes effect instantly without redeploying code.

### 2.3 Dynamic Plan Builder
- Super Admin can create **new plans at any time** through a UI (no code changes): set name, price, billing cycle, limits (teachers/students/storage/courses), enabled features, and commission rate.
- Plans support **add-ons** — optional paid extras a tenant can attach to their base plan (e.g., "+5GB storage", "+Live Classes module", "+SMS credits bundle").

### 2.4 Dynamic Menu & Dashboard Widgets
- Sidebar menus and dashboard widgets per role are generated from a configuration table, not hardcoded in Blade files — allowing Super Admin to reorder, rename, hide, or add new menu entries per role without a code release.

---

## 3. PAYMENT GATEWAY INTEGRATION ARCHITECTURE (DEEP DIVE)

### 3.1 Unified Payment Abstraction Layer
Rather than hardcoding each gateway everywhere in the app, ELMS uses a **Payment Driver Pattern** (similar to Laravel's own Filesystem/Mail driver system):

- A `PaymentGatewayInterface` defines standard methods every driver must implement: `initiate()`, `verify()`, `handleWebhook()`, `refund()`, `payout()`.
- Each real gateway (Stripe, PayPal, M-Pesa, Tigo Pesa, Airtel Money, Pesapal, Flutterwave, Selcom, DPO Pay) is implemented as its own **Driver class** conforming to that interface.
- A `PaymentManager` service resolves the correct driver at runtime based on what the tenant/customer selects at checkout, or based on Super Admin's configured priority order per country/currency.
- Adding a brand-new gateway in the future means writing **one new driver class** — no changes needed anywhere else in the checkout flow.

### 3.2 Supported Payment Categories

**A. International / Card Payments**
- Stripe (Cards, Apple Pay, Google Pay)
- PayPal

**B. Local Mobile Money (East Africa)**
- M-Pesa (Safaricom/Vodacom)
- Tigo Pesa
- Airtel Money
- Halopesa (Tanzania)

**C. Local Payment Aggregators**
- Pesapal
- Flutterwave
- Selcom (Tanzania)
- DPO Pay (Direct Pay Online)

### 3.3 Checkout Flow (Generic, Gateway-Agnostic)
1. User selects a plan (Admin/Solo Teacher) or a course (Student) at checkout.
2. System detects the tenant's/user's region/currency and shows the relevant available gateways (configurable priority list, e.g., Tanzanian users see Mobile Money first, international users see Stripe/PayPal first).
3. User picks a method and confirms payment.
4. ELMS calls the selected driver's `initiate()` method, which returns either a redirect URL (card gateways) or a push prompt (mobile money STK Push).
5. Gateway sends a **webhook callback** to a unified endpoint: `/api/v1/payments/webhook/{gateway}`.
6. The `PaymentManager` verifies the webhook signature, matches it to the pending transaction, and marks it as `paid`, `failed`, or `pending`.
7. On success: subscription is activated/renewed, or course enrollment is created, and a receipt/invoice is generated and emailed/SMS'd to the user.
8. All transactions — regardless of gateway — are stored in one unified `transactions` table for consistent reporting.

### 3.4 Payout Flow (For Solo Teacher Wallets)
- Solo Teacher requests a withdrawal from their Wallet.
- Request enters a `withdrawals` queue with status `pending`.
- Depending on configuration, payouts are either:
  - **Manual** — Super Admin reviews and triggers payment manually via bank/mobile money, then marks as `completed`.
  - **Automated** — system calls the gateway's payout/disbursement API directly (e.g., M-Pesa B2C API, Flutterwave Transfers API) to send funds automatically.
- Minimum withdrawal thresholds and payout fees are configurable per plan.

### 3.5 Reliability & Failover
- If a preferred gateway is down (detected via failed health-check pings or repeated failures), the checkout screen can automatically hide it and highlight an alternative gateway, so payment collection is never fully blocked by one provider's downtime.
- All webhook events are logged raw (before processing) into a `payment_webhook_logs` table for auditing/debugging, even if processing later fails — nothing is silently lost.
- Idempotency keys are used to make sure a webhook retried by the gateway does not create duplicate transactions.

### 3.6 Security for Payments
- Webhook signature verification per gateway (each gateway has its own secret/signing method).
- No sensitive card data ever touches ELMS's own servers directly — hosted checkout pages / tokenization used wherever possible (PCI-DSS friendly approach).
- All payment credentials (API keys/secrets) stored encrypted and manageable only by Super Admin via a secure settings panel.

---

## 4. SMS INTEGRATION ARCHITECTURE (DEEP DIVE)

### 4.1 Unified SMS Abstraction Layer
Just like payments, SMS follows a **Driver Pattern**:
- An `SmsGatewayInterface` defines: `send()`, `sendBulk()`, `checkDeliveryStatus()`.
- Drivers implemented for: **Africa's Talking**, **Beem Africa**, **NextSMS**, **Twilio** (for international numbers), and any future local aggregator.
- Super Admin configures which SMS provider is active (and can even set up **fallback provider chains** — if Provider A fails, automatically retry via Provider B).

### 4.2 SMS Use Cases in ELMS
| Trigger | Example Message Purpose |
|---|---|
| Account registration | OTP verification code |
| Password reset | One-time reset code |
| Payment confirmation | "Your payment of TZS X for Course Y was received." |
| Exam/quiz results published | "Your result for Quiz X is now available." |
| Assignment deadline reminder | "Your assignment for Course X is due tomorrow." |
| Live class starting soon | "Your live class starts in 15 minutes." |
| Subscription about to expire | "Your institution's plan expires in 3 days." |
| Payout processed | "Your withdrawal of TZS X has been sent." |
| New enrollment (to teacher) | "A new student has joined your course." |

### 4.3 SMS Template Engine
- All SMS content is stored as **editable templates** (not hardcoded strings), with placeholders like `{{student_name}}`, `{{course_title}}`, `{{amount}}`.
- Templates can be edited by Super Admin (platform-wide defaults) and optionally customized per Institution (Admin can adjust tone/language for their own tenant, within character-limit rules).
- Multi-language template variants (English/Swahili) selected automatically based on the recipient's language preference.

### 4.4 Bulk SMS & Campaigns
- Admins/Solo Teachers can send **bulk announcements** via SMS to a filtered list of students (e.g., "all students in Course X who haven't completed Module 2").
- SMS credits are tracked per tenant (prepaid **SMS Wallet**, separate from the earnings Wallet) — Admin/Solo Teacher purchases SMS credit bundles as an add-on.
- Delivery reports are fetched asynchronously (via provider webhook or polling) and shown in a "Campaign Report" (sent/delivered/failed counts).

### 4.5 SMS Queueing & Rate Limiting
- All outbound SMS go through Laravel Queues to avoid blocking user requests and to allow retrying failed sends.
- Rate-limiting per tenant prevents one tenant's bulk campaign from overwhelming shared SMS provider limits.
- Cost estimation shown before sending a bulk campaign (based on recipient count and per-SMS cost from provider).

### 4.6 Compliance & Opt-Out
- Students can opt out of non-critical SMS categories (marketing/announcements) while remaining subscribed to critical ones (OTP, exam results, payment confirmations).
- Opt-out status stored per user, checked before every non-critical send.

---

## 5. DYNAMIC COURSE BUILDER ENHANCEMENTS

- **Drag-and-drop course builder** — reorder modules/lessons visually without page reloads (Livewire/Vue powered).
- **Reusable Content Blocks** — a teacher can save a lesson block (e.g., a standard intro video) and reuse it across multiple courses instantly.
- **Conditional Release Rules** — lessons can be configured to unlock only after: a specific date, completion of a prior lesson, passing a quiz with a minimum score, or manual teacher approval.
- **Course Cloning** — duplicate an entire course structure (including quizzes) as a starting template for a new course or new academic term/cohort.
- **Multi-format Lesson Content** — mix video, text, downloadable file, embedded external link, and live session inside the same lesson flow.

---

## 6. DYNAMIC FORMS & CUSTOM FIELDS ENGINE

- Admin/Solo Teacher can add **custom fields** to registration forms, course applications, or student profiles without needing a developer (e.g., an institution wants to collect "National ID Number" or a Solo Teacher wants to ask "What's your current job title?" before enrollment).
- Custom fields support types: text, number, dropdown, checkbox, date, file upload.
- Captured custom field data is stored in a flexible JSON column so no schema migration is needed every time a new field is added.

---

## 7. WORKFLOW AUTOMATION & RULES ENGINE

A lightweight "if this, then that" automation engine lets Admins/Solo Teachers set rules without code:

**Examples of automation rules:**
- "If a student completes 100% of a course -> automatically generate and email their certificate."
- "If a student hasn't logged in for 14 days -> send a re-engagement email + SMS."
- "If a quiz score is below 50% -> automatically notify the teacher."
- "If a subscription payment fails 3 times -> automatically suspend the tenant and notify Super Admin."
- "If a course purchase is refunded -> automatically revoke enrollment access."

This is implemented as an **Events + Listeners** system in Laravel, exposed to Admins through a simple visual rule-builder UI (trigger -> condition -> action).

---

## 8. PLUGIN/EXTENSION SYSTEM (APP MARKETPLACE FOR THE LMS ITSELF)

- A lightweight plugin architecture allows new modules (e.g., a "Zoom Deep Integration Pack" or "Advanced Proctoring Pack") to be installed/activated per tenant without modifying the core codebase.
- Plugins register their own routes, permissions, and settings pages through a defined Service Provider contract.
- This allows the platform owner (Super Admin) to eventually offer a **plugin marketplace** where third-party developers build add-ons for ELMS, similar to WordPress plugins.

---

## 9. MULTI-CURRENCY & MULTI-REGION SUPPORT

- Base currency configurable per tenant (e.g., TZS, KES, UGX, USD).
- Course prices can be set in the tenant's local currency and automatically displayed in a visitor's preferred currency using live/cached exchange rates.
- Tax/VAT rules configurable per region (some institutions may need to show VAT-inclusive pricing).
- Region-aware payment gateway suggestions (see Section 3.3).

---

## 10. DYNAMIC THEMING & WHITE-LABEL BRANDING

- Enterprise Institutions and top-tier Solo Teacher plans can customize:
  - Logo, favicon, color palette, and font choice.
  - Custom domain/subdomain (e.g., `learn.myschool.com` instead of `myschool.elms.com`).
  - Custom email sender name and footer branding.
- Theme settings are stored per tenant and applied dynamically at runtime (CSS variables injected based on tenant configuration) — no separate codebase per client.

---

## 11. ADVANCED CUSTOM REPORT BUILDER

- Beyond the fixed dashboards (Section 14 of Part 1), Admins and Super Admin get a **drag-and-drop report builder**:
  - Choose data source (students, courses, payments, quiz results).
  - Choose filters (date range, course, cohort, plan).
  - Choose visualization (table, bar chart, line chart, pie chart).
  - Save custom reports for reuse, schedule them to auto-email as PDF/Excel weekly or monthly.

---

## 12. WEBHOOKS & DEVELOPER API

- ELMS can send **outgoing webhooks** to a tenant's own external systems for events like `student.enrolled`, `course.completed`, `payment.succeeded`, `certificate.issued` — useful for institutions that want to sync data into their own school management systems.
- A **Developer API Keys** section lets Enterprise tenants generate API keys with scoped permissions to build their own integrations against ELMS's public API (Section 11 of Part 1).

---

## 13. ADVANCED NOTIFICATION ENGINE (TEMPLATES + TRIGGERS)

- All notifications (email, SMS, in-app, push) are driven by an editable **Notification Template Library**, similar to the SMS templates in Section 4.3, but covering every channel.
- Each template can be toggled on/off per channel per event (e.g., "Course completed" triggers Email + In-App, but not SMS, by default — configurable per tenant).
- A visual **Notification Trigger Matrix** in the Admin panel shows every system event down one side and every channel across the top, letting Admins simply tick/untick which combinations are active.

---

## 14. DYNAMIC PRICING ENGINE

- Course prices can have **scheduled promotions** (e.g., automatic 30% off for the first 7 days after publishing).
- **Tiered pricing** — early-bird price for the first N students, then price increases automatically.
- **Bundle discounts** — buying 3+ courses from the same Solo Teacher automatically applies a bundle discount.
- **Geo-based pricing** — optional ability to set different price points for different countries/regions to match local purchasing power.
- **Coupon Engine** — percentage or fixed discounts, usage limits, expiry dates, and restriction to specific courses/plans.

---

## 15. CONTENT VERSIONING & APPROVAL WORKFLOWS

- Every course edit creates a new **draft version** while the currently published version remains live for existing students — changes go live only after the teacher/Solo Teacher explicitly publishes the update.
- Optional **Institution-level Approval Workflow** — Admin can require that any new course/quiz built by a Teacher must be reviewed and approved before it becomes visible to students.
- Optional **Platform-level Moderation** — Super Admin can require Solo Teacher courses to pass a review step before appearing in the public Marketplace (toggleable per plan or globally).

---

## 16. AI-ENHANCED FEATURES

- **AI Quiz Generator** — teacher uploads lesson text/transcript, and the system suggests draft quiz questions for review before publishing.
- **AI Content Summarizer** — auto-generate a short lesson summary/description from an uploaded video transcript.
- **AI Chat Assistant for Students** — a course-scoped assistant that answers student questions based only on that course's material (reduces repetitive questions to teachers).
- **AI-Assisted Grading Suggestions** — for essay-type answers, AI suggests a draft score/feedback that the teacher can review, edit, and approve (human always stays in control of final grades).

---

## 17. ADVANCED MARKETPLACE GROWTH FEATURES

- **Affiliate/Referral Program** — any user can generate a referral link for a Solo Teacher's course and earn a commission on resulting sales, tracked via a `referrals` table and cookie/URL parameter attribution.
- **Featured Courses & Sponsored Placement** — Solo Teachers can pay extra to be featured on the Marketplace homepage (additional revenue stream for the platform).
- **Bundled Marketplace Search** — full-text search with filters for price, rating, duration, language, and skill level (powered by Laravel Scout + Meilisearch).
- **Wishlist & Abandoned Cart Reminders** — automatic email/SMS nudges to students who viewed a course but didn't purchase.

---

## 18. MOBILE & OFFLINE-FIRST ENHANCEMENTS

- Since the API layer (Part 1, Section 11) is already versioned and role-scoped, a native mobile app can be built later to consume it directly.
- **Offline Lesson Download** (future mobile feature) — students can download video/materials for offline viewing in low-connectivity areas, with encrypted local storage to protect content.
- **Progressive Web App (PWA)** mode for the web platform itself, allowing "add to home screen" and limited offline caching even before a native app exists.

---

## 19. ADVANCED SECURITY ADD-ONS

- **Proctoring/Exam Integrity Mode** — optional webcam snapshot or tab-switch detection during high-stakes quizzes (configurable per quiz).
- **IP Allow-listing** for Institution Admin panels (large institutions may want to restrict admin login to office IP ranges).
- **Granular Audit Trail Export** — Super Admin/Admin can export a full audit log for compliance reviews.
- **Automated Anomaly Alerts** — flags unusual patterns (e.g., one account completing 10 courses in one hour) for manual review.

---

## 20. SCALABILITY PATH TOWARD MICROSERVICES

While ELMS starts as a modular monolith, the dynamic architecture described above (driver patterns, event-based automation, plugin system) is intentionally designed so that, if the platform grows very large, individual modules can be extracted into standalone services with minimal rework:
- **Video Processing Service** — first candidate for extraction (CPU-heavy, independent scaling needs).
- **Notification Service** (Email/SMS/Push dispatch) — second candidate, since it's already queue-based and loosely coupled.
- **Payment Service** — can be isolated behind an internal API once transaction volume justifies it, for stronger PCI-style boundary separation.

---

## 21. CONCLUSION

This companion document pushes ELMS from "a working LMS" to a **truly dynamic, configuration-driven platform** — one where Super Admin and Admins can reshape pricing, branding, payment options, SMS behavior, automation rules, and even add new modules over time, largely without waiting on new code deployments. Combined with the driver-pattern approach to Payment Gateways and SMS providers, the platform is built to expand into new markets, currencies, and providers with minimal engineering effort — which is exactly what a growing, multi-tenant education business needs.

---

*This document should be read together with "ELMS_Architecture.md" (Part 1). Together they form the full architectural reference for building ELMS on Laravel.*
