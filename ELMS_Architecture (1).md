# ELMS — E-Learning Management System
## Complete System Architecture Document

**Core Technology:** Laravel (PHP Framework)
**System Type:** Multi-Tenant SaaS Learning Management System
**Document Version:** 2.0 (Extended Edition)

---

## TABLE OF CONTENTS

1. Introduction & Vision
2. Goals & Objectives
3. Glossary of Terms
4. System Actors & Roles (Full Permission Matrix)
5. Multi-Tenancy Architecture
6. Subscription & Billing System
7. Core Modules (Full Feature Breakdown)
8. Database Design (Conceptual Entity Model)
9. Technology Stack
10. Non-Functional Requirements
11. API Architecture
12. Notification System
13. Security Architecture
14. Analytics, Reporting & KPIs
15. UI/UX & Dashboard Design Principles
16. Third-Party Integrations
17. Deployment & Infrastructure Architecture
18. Testing Strategy
19. Risk Assessment & Mitigation
20. Development Roadmap
21. Future Expansion Ideas
22. Conclusion

---

## 1. INTRODUCTION & VISION

ELMS is a comprehensive, multi-tenant e-learning platform built to serve two very different but equally important markets at the same time:

1. **Institutions** (schools, colleges, universities, training centers) that need a private, internal system to manage their own teachers and students.
2. **Independent instructors ("Solo Teachers")** who want to build, publish, and sell their own courses directly to the public — similar to a personal Udemy/Teachable-style storefront — while paying for the platform themselves.

The platform is designed as a **modular monolith** in Laravel, meaning every feature (courses, quizzes, payments, video, reporting) lives in its own clearly separated module inside a single Laravel codebase, making it easy to maintain now and easy to split into microservices later if the platform grows very large.

The vision is to create **one unified engine** that powers three different experiences:
- A private LMS for schools (B2B).
- A public course marketplace for independent instructors (B2C/Creator economy).
- A central control tower for the platform owner (Super Admin) to manage billing, growth, and system health.

---

## 2. GOALS & OBJECTIVES

- Allow institutions to onboard, manage staff/students, and run their entire academic delivery digitally.
- Allow independent instructors to register, pay for a plan, and immediately start monetizing their knowledge.
- Provide a complete course-building toolkit: video hosting, structured lessons, quizzes, assignments, grading, and certificates.
- Provide a robust subscription and billing engine that is the financial backbone of the whole platform.
- Provide deep analytics at every level (platform-wide, institution-wide, per-teacher, per-student).
- Be secure, scalable, mobile-ready, and easy to extend with new features over time.
- Support both local payment methods (Mobile Money: M-Pesa, Tigo Pesa, Airtel Money) and international payment methods (Stripe, PayPal, cards).

---

## 3. GLOSSARY OF TERMS

| Term | Meaning |
|---|---|
| Tenant | An isolated account space — either an Institution or a Solo Teacher |
| Super Admin | The platform owner who controls the entire system |
| Admin | The owner/manager of an Institution tenant |
| Solo Teacher | An independent instructor tenant who sells their own courses |
| Course | A structured learning product made of modules and lessons |
| Enrollment | The relationship of a student being registered into a course |
| Assessment | A quiz, test, assignment, or exam used to measure learning |
| Cohort | A group/batch of students moving through a course together |
| Drip Content | Content released gradually over time instead of all at once |
| Marketplace | The public storefront where Solo Teacher courses are listed for sale |
| Payout | Withdrawal of earned money from the in-system wallet to a bank/mobile money account |

---

## 4. SYSTEM ACTORS & ROLES (FULL PERMISSION MATRIX)

### 4.1 Super Admin (Platform Owner)
Responsibilities:
- Full visibility over all tenants (Institutions and Solo Teachers).
- Create, edit, disable subscription plans and their pricing.
- Suspend/reactivate any tenant for non-payment or policy violation.
- View platform-wide revenue, growth, and churn analytics.
- Manage global settings: payment gateway keys, email/SMS templates, terms of service, commission rates.
- Approve or reject courses submitted by Solo Teachers (optional moderation workflow).
- Manage support tickets escalated from Admins/Solo Teachers.
- Manage feature flags per plan (e.g., "Certificate Generator" only available on Pro plan and above).
- Access system health dashboard (server load, queue status, storage usage).

### 4.2 Admin (Institution Owner/Manager)
Responsibilities:
- Register and manage the institution's own tenant space.
- Invite and manage Teachers (add/remove/suspend).
- Invite and manage Students (add individually or bulk-import via Excel/CSV).
- Create departments, classes, or cohorts.
- Set internal academic calendar (terms, semesters).
- View institution-wide analytics (per teacher, per class, per subject).
- Manage internal billing (e.g., collecting fees from students, if enabled).
- Cannot see data belonging to other institutions or Solo Teachers (strict tenant isolation).
- Can optionally sub-delegate limited admin rights to a "Department Head" role.

### 4.3 Teacher (Institution Staff)
Responsibilities:
- Create and manage courses **within their institution only** (not public marketplace by default).
- Upload videos, documents, slides, and other learning material.
- Build quizzes, assignments, and exams.
- Grade manual assessments (essays, file submissions).
- View performance reports limited to their own students/classes.
- Communicate with students via announcements, comments, and messaging.
- Cannot manage billing or add other teachers.

### 4.4 Student
Responsibilities/Access:
- Enroll in institution courses (invited) or marketplace courses (self-purchased from Solo Teachers).
- View one unified dashboard even if enrolled across multiple institutions/instructors.
- Watch videos, read materials, complete quizzes/assignments.
- Track personal progress and download certificates upon completion.
- Participate in discussion forums/comment threads under lessons.
- Receive notifications (in-app, email, SMS) about deadlines, grades, and announcements.

### 4.5 Solo Teacher (Independent Instructor / Creator)
Responsibilities/Access:
- Register independently — no institution required.
- Subscribes to and pays for their own "Creator Plan."
- Full course-builder access: create unlimited (per-plan-limit) courses, modules, lessons.
- Upload and publish videos directly (acts like a private, structured YouTube channel).
- Set pricing per course (free, one-time paid, or bundled).
- Publish courses to the public **Marketplace** for anyone to discover and purchase.
- Build quizzes/assessments and issue certificates.
- View sales analytics: revenue, number of students, course ratings, completion rates.
- Manage a personal **Wallet**: view earnings, request payouts to mobile money/bank.
- Respond to student reviews and forum questions.
- Cannot add other teachers under them by default (may unlock a "Team Add-on" in a future plan tier).

### 4.6 Full Permission Matrix (Summary)

| Capability | Super Admin | Admin | Teacher | Solo Teacher | Student |
|---|---|---|---|---|---|
| Manage platform settings | Yes | No | No | No | No |
| Manage subscription plans | Yes | No | No | No | No |
| View all tenants | Yes | No | No | No | No |
| Manage own institution | No | Yes | No | No | No |
| Add/remove teachers | No | Yes | No | No | No |
| Add/remove students | No | Yes | View only | View only | No |
| Create courses | No | No | Yes (internal) | Yes (public) | No |
| Publish to marketplace | No | No | No | Yes | No |
| Create quizzes/assessments | No | No | Yes | Yes | No |
| Grade assignments | No | No | Yes | Yes | No |
| Withdraw earnings (payout) | No | No | No | Yes | No |
| Enroll in courses | No | No | No | No | Yes |
| View own reports/certificates | No | No | No | No | Yes |
| Pay platform subscription | No (owns platform) | Yes | No | Yes | No |

---

## 5. MULTI-TENANCY ARCHITECTURE

### 5.1 Chosen Approach
**Single Database, Shared Schema with `tenant_id` scoping**, upgradeable later to **Database-per-Tenant** for very large enterprise clients.

- Every Institution gets a unique `tenant_id`.
- Every Solo Teacher is also treated as their own single-user tenant.
- Global Laravel Eloquent Scopes automatically filter every query by the logged-in user's `tenant_id`, so no developer can accidentally leak data between tenants.
- A Middleware layer (`IdentifyTenant`) runs on every request to resolve and bind the correct tenant context before any controller logic executes.

### 5.2 Data Isolation Rules
- No cross-tenant data visibility by default — Admin A can never see Admin B's students, and one Solo Teacher can never see another's course data.
- The Marketplace is the **only shared, cross-tenant surface** — it exposes published course metadata (title, description, price, preview) publicly, while keeping actual lesson content and student data private to the owning tenant.

### 5.3 Scaling Path
- Phase 1: Shared database + `tenant_id` (fast to build, cheap to host).
- Phase 2: Move large Enterprise clients to isolated databases using a package like `stancl/tenancy` if/when performance or compliance requires it.
- Phase 3 (optional, long-term): Split heavy modules (video processing, quiz engine) into separate services communicating via queues/APIs.

---

## 6. SUBSCRIPTION & BILLING SYSTEM

### 6.1 Plan Categories

**Institution Plans**

| Plan | Teachers | Students | Storage | Key Features |
|---|---|---|---|---|
| Basic | Up to 5 | Up to 100 | 10 GB | Core LMS features |
| Pro | Up to 25 | Up to 1,000 | 100 GB | + Certificates, Advanced Reports, Bulk Import |
| Enterprise | Unlimited | Unlimited | Custom | + API Access, White-label, Dedicated Support |

**Solo Teacher (Creator) Plans**

| Plan | Courses | Storage | Key Features |
|---|---|---|---|
| Starter | Up to 3 | 5 GB | Basic course builder, 50 students max |
| Growth | Up to 15 | 50 GB | Marketplace listing, Coupons, Wallet payouts |
| Pro Creator | Unlimited | 200 GB | Priority marketplace placement, Advanced analytics, Lower commission rate |

### 6.2 Billing Mechanics
- **Recurring Billing** — monthly/annual cycles via Laravel Cashier (Stripe) for international cards, plus custom driver integrations for local mobile money providers.
- **Free Trial Periods** — configurable trial length per plan (e.g., 14 days).
- **Usage Limit Enforcement** — the system automatically blocks actions once a tenant hits their plan's limit (e.g., cannot add the 101st student on a 100-student plan), with an in-app prompt to upgrade.
- **Proration** — mid-cycle upgrades/downgrades are billed proportionally.
- **Coupons & Discount Codes** — for both platform subscriptions and individual course purchases.
- **Invoicing** — auto-generated PDF invoices/receipts for every transaction.
- **Dunning Management** — automatic retry and reminder emails for failed payments before suspension.

### 6.3 Solo Teacher Revenue Model
- Course sales money is credited to an in-app **Wallet**.
- The platform deducts a **commission percentage** (configurable per plan, e.g., 20% on Starter, 10% on Pro Creator) before crediting the wallet.
- Solo Teachers can request **Payouts** to mobile money or bank accounts; Super Admin approves/processes payout requests (manual or automated via payment gateway payout APIs).
- Full transaction ledger visible to the Solo Teacher (sales, commissions, payouts, refunds).

### 6.4 Refunds & Disputes
- Configurable refund policy window (e.g., 7-day money-back guarantee).
- Refund requests go through an approval workflow (Solo Teacher or Super Admin depending on policy).
- Automatic reversal of wallet credit on approved refunds.

---

## 7. CORE MODULES (FULL FEATURE BREAKDOWN)

### 7.1 User & Identity Management
- Registration, login, email verification, password reset, social login (Google/Facebook) optional.
- Role-Based Access Control using `spatie/laravel-permission`.
- Two-Factor Authentication (2FA) for Admin/Super Admin accounts.
- Profile management: avatar, bio, contact info, notification preferences.
- Account deactivation/reactivation and full data export/delete requests (GDPR-style).

### 7.2 Course Management
- Course structure: **Course -> Modules/Sections -> Lessons -> Content Items (video/PDF/text/link/SCORM optional)**.
- Course categories, subcategories, and tags for discoverability.
- Drip-feed content scheduling (release lessons over time or based on completion of previous lesson).
- Prerequisites (a student must finish Course A before accessing Course B).
- Course versioning (edit a live course without breaking currently-enrolled students' progress).
- Draft/Published/Archived course states.
- Bundles (group multiple courses into one discounted package).

### 7.3 Video & Content Delivery
- Chunked video upload for large files with progress bar and resumable uploads.
- Backend video transcoding via FFmpeg (queued jobs) into multiple resolutions (adaptive HLS streaming).
- Storage on S3-compatible object storage (AWS S3 / DigitalOcean Spaces).
- Signed, time-limited streaming URLs to prevent direct link sharing.
- Optional dynamic watermarking overlay (student name/email) to discourage screen-recording piracy.
- Video progress/resume tracking per student.
- Support for supplementary downloadable resources (PDF notes, slides, code samples).

### 7.4 Quiz & Assessment Engine
- Question types: Multiple Choice, True/False, Fill-in-the-blank, Short Answer, Essay, File Upload assignments, Matching, Ordering.
- Question Bank with randomized question selection per attempt (anti-cheating).
- Timed quizzes with auto-submit on timeout.
- Configurable attempt limits and cooldown periods between retakes.
- Auto-grading for objective questions; manual grading workflow for essays/assignments with rubric support.
- Plagiarism-flagging hooks for text submissions (optional third-party integration).
- Randomized answer-order shuffling per student.

### 7.5 Grading, Results & Certificates
- Centralized Gradebook per teacher/course.
- Weighted grading (quizzes, assignments, participation contributing different percentages to final grade).
- Individual student report cards (PDF export).
- Auto-generated Certificates of Completion with unique verification codes/QR codes.
- Public certificate verification page (anyone can scan/check authenticity).

### 7.6 Live Classes & Webinars (Add-on Module)
- Integration with Zoom/Google Meet APIs to schedule and embed live sessions inside a course.
- Automatic recording upload into the course's video library after the session ends.
- Attendance tracking for live sessions.

### 7.7 Assignments & Submissions
- File-based assignment submission portal.
- Deadline management with late-submission penalties (configurable).
- Turnitin-style similarity check integration (optional third-party).
- Inline feedback/annotation on submitted files.

### 7.8 Attendance & Scheduling
- Class timetables/calendars for institutions.
- Daily/weekly attendance marking for physical or hybrid classes.
- Calendar sync (Google Calendar/Outlook) for deadlines and live sessions.

### 7.9 Communication & Notifications
- In-app notification center.
- Email notifications via Laravel Notifications + Queue workers.
- SMS notifications via local gateways (for critical alerts like exam results).
- Push notifications (for future mobile app via Firebase Cloud Messaging).
- Announcements broadcast from Admin/Teacher to Students.
- Discussion forums/Q&A threads under every lesson.
- Direct messaging between students and teachers.

### 7.10 Marketplace & Discovery (Solo Teacher Storefront)
- Public-facing course catalog with search, filters, categories, and ratings.
- Course landing pages with preview video, syllabus, instructor bio, and reviews.
- Ratings & Reviews system with moderation.
- Coupon codes and limited-time promotional pricing.
- SEO-friendly course URLs and metadata for organic discovery.
- Affiliate/referral links (optional future feature) allowing others to promote a Solo Teacher's course for a commission.

### 7.11 Analytics & Dashboards
Covered in detail in Section 14.

### 7.12 Payments & Wallet
- Stripe/PayPal for international payments.
- M-Pesa, Tigo Pesa, Airtel Money, Pesapal, Flutterwave for local East African payments.
- Wallet ledger, payout requests, and transaction history (see Section 6).

### 7.13 Content Security & Anti-Piracy
- Video watermarking and signed URLs (see 7.3).
- Download restrictions on protected files unless explicitly allowed by the content owner.
- Device/session limits (e.g., max 2 concurrent active sessions per student account).
- IP/device anomaly detection flags for suspicious account sharing.

### 7.14 Support & Helpdesk
- Ticketing system for Admins/Solo Teachers to reach Super Admin support.
- Knowledge base / FAQ module for self-service support.
- Live chat widget integration (optional third-party, e.g., Tawk.to or Crisp).

### 7.15 Localization & Accessibility
- Multi-language interface support (English, Swahili, and extensible to others via Laravel localization files).
- RTL-ready layout structure (future-proofing).
- Accessibility considerations (screen-reader-friendly markup, captions/subtitles support for videos).

### 7.16 Gamification (Optional Enhancement Module)
- Points, badges, and leaderboards to increase student engagement.
- Streaks for consistent daily learning.
- Achievement certificates for milestones (not just full course completion).

---

## 8. DATABASE DESIGN (CONCEPTUAL ENTITY MODEL)

This section describes entities and relationships conceptually (no code), to guide actual schema design later.

### 8.1 Core Identity & Tenancy
- `users` — all human accounts (super_admin, admin, teacher, student, solo_teacher), linked to a `role_id`.
- `tenants` — one row per Institution or Solo Teacher, with `type` (institution/solo) and `plan_id`.
- `plans` — subscription plan definitions and their limits (max_teachers, max_students, storage_limit_gb, price, billing_cycle).
- `subscriptions` — billing history/status per tenant (active/expired/cancelled/trialing).
- `roles` / `permissions` — RBAC definitions (via Spatie package tables).

### 8.2 Academic Structure
- `courses` — `tenant_id`, `owner_id` (teacher/solo teacher), `price`, `status`, `visibility` (private/marketplace).
- `course_modules` — sections within a course.
- `lessons` — individual learning units (video/pdf/text/link) inside a module.
- `enrollments` — pivot linking `students` to `courses`, with `progress_percentage` and `enrolled_at`.
- `cohorts` — grouping of students for institution-based scheduling.
- `attendance_records` — daily/session attendance per student.

### 8.3 Assessment
- `quizzes` — linked to a lesson or course.
- `questions` / `question_options` — question bank content.
- `quiz_attempts` / `quiz_answers` — student attempt history and answers.
- `assignments` / `assignment_submissions` — file-based assessments and their grading state.
- `results` — final consolidated grades per student per course.
- `certificates` — issued certificates with verification codes.

### 8.4 Commerce
- `transactions` — every payment event (subscription payments, course purchases, refunds).
- `wallets` — Solo Teacher earning balances.
- `withdrawals` — payout requests and their status.
- `coupons` — discount codes and their rules.
- `invoices` — generated billing documents.

### 8.5 Engagement
- `notifications` — system-wide notification log.
- `announcements` — broadcast messages from Admin/Teacher.
- `forum_threads` / `forum_replies` — discussion content per lesson/course.
- `reviews` — course ratings and written reviews.
- `messages` — direct messaging between users.

### 8.6 Key Relationships Summary
- A `Tenant` **hasMany** `Users`.
- A `Teacher`/`SoloTeacher` **hasMany** `Courses`.
- A `Course` **hasMany** `Modules` -> a `Module` **hasMany** `Lessons`.
- A `Course` **belongsToMany** `Students` (through `enrollments`).
- A `Quiz` **belongsTo** a `Lesson`/`Course` and **hasMany** `Questions`.
- A `Student` **hasMany** `QuizAttempts` and `AssignmentSubmissions`.
- A `SoloTeacher` **hasOne** `Wallet`, which **hasMany** `Withdrawals`.

---

## 9. TECHNOLOGY STACK

| Layer | Technology |
|---|---|
| Backend Framework | Laravel (latest LTS version) |
| Frontend / Admin Panels | Blade + Livewire, or Laravel + Vue/React via Inertia.js |
| Authentication | Laravel Breeze/Fortify + Sanctum (for API/mobile tokens) |
| Roles & Permissions | `spatie/laravel-permission` |
| Multi-Tenancy | Global Scopes + Middleware (upgradeable to `stancl/tenancy`) |
| Payments (International) | Laravel Cashier (Stripe), PayPal SDK |
| Payments (Local/Mobile Money) | Custom API integrations: M-Pesa, Tigo Pesa, Airtel Money, Pesapal, Flutterwave |
| Video Processing | FFmpeg + Laravel Queues (background jobs) |
| File Storage | AWS S3 / DigitalOcean Spaces via Laravel Filesystem |
| Search | Laravel Scout + Meilisearch/Algolia (course discovery) |
| Notifications | Laravel Notifications + Redis Queue |
| PDF Generation | `barryvdh/laravel-dompdf` or `spatie/laravel-pdf` (certificates, invoices, reports) |
| Real-Time Features | Laravel Reverb or Pusher (live notifications, chat, live class status) |
| Background Jobs | Redis + Laravel Horizon (monitor video processing, emails, reports) |
| API Layer | Laravel API Resources + Sanctum (for future mobile apps) |
| Caching | Redis |
| Testing | PHPUnit / Pest |
| CI/CD | GitHub Actions / GitLab CI |
| Deployment | Docker + Laravel Forge/Vapor, or a standard VPS with Nginx + Supervisor |
| Monitoring | Laravel Telescope (dev), Sentry/Bugsnag (error tracking), Laravel Pulse (performance) |

---

## 10. NON-FUNCTIONAL REQUIREMENTS

- **Performance:** Page loads under 2 seconds on average; video start time under 3 seconds with adaptive streaming.
- **Scalability:** Horizontally scalable web servers behind a load balancer; queue workers scaled independently for video processing spikes.
- **Availability:** Target 99.9% uptime; automated health checks and failover for critical services.
- **Data Backup:** Daily automated database backups with point-in-time recovery; video storage redundancy via cloud object storage replication.
- **Compliance:** Data privacy practices aligned with GDPR-style principles; clear data retention and deletion policies.
- **Maintainability:** Modular monolith structure keeps each feature area independently testable and upgradeable.
- **Localization Readiness:** All user-facing text externalized into language files from day one.

---

## 11. API ARCHITECTURE

The system exposes a versioned RESTful API (`/api/v1/...`) from the start, even if the first release is web-only, so a future mobile app requires no backend rewrite.

**Key API groups:**
- `/api/v1/auth/*` — registration, login, token refresh, password reset.
- `/api/v1/courses/*` — browse marketplace, course details, enrollment.
- `/api/v1/lessons/*` — fetch lesson content, mark progress.
- `/api/v1/quizzes/*` — start attempt, submit answers, fetch results.
- `/api/v1/payments/*` — initiate payment, webhook callbacks from gateways.
- `/api/v1/wallet/*` — balance, transaction history, payout requests (Solo Teacher).
- `/api/v1/reports/*` — role-scoped analytics endpoints.
- `/api/v1/notifications/*` — fetch/mark-as-read notifications.

All endpoints are protected by Sanctum tokens and scoped by the authenticated user's role and tenant.

---

## 12. NOTIFICATION SYSTEM

| Channel | Use Cases |
|---|---|
| In-App | Grades posted, new announcement, forum reply, enrollment confirmation |
| Email | Welcome emails, invoices, password resets, course completion certificates |
| SMS | Exam results, urgent deadline reminders, payment confirmations |
| Push (future mobile) | Live class starting soon, new lesson available |

All notifications are queued (not sent synchronously) to avoid slowing down the user's request, using Laravel's Notification + Queue system.

---

## 13. SECURITY ARCHITECTURE

- **Tenant Isolation:** Enforced at the Eloquent query level via global scopes — no controller can accidentally bypass it.
- **Role-Based Middleware:** Every route group is protected according to the required role.
- **Rate Limiting:** Applied to login, registration, and API endpoints to prevent brute-force attacks.
- **Signed URLs:** Used for all sensitive file/video access with short expiry windows.
- **Two-Factor Authentication:** Required/optional for Admin and Super Admin accounts.
- **Audit Logging:** Every critical action (delete, role change, payout approval) is logged with actor, timestamp, and before/after state.
- **Encryption:** Sensitive fields (payment details, personal identifiers) encrypted at rest.
- **Session & Device Controls:** Configurable concurrent session limits per student account to reduce credential sharing.
- **Regular Dependency Audits:** Automated scanning of Composer/NPM packages for known vulnerabilities.

---

## 14. ANALYTICS, REPORTING & KPIs

### 14.1 Super Admin Dashboard
- Total tenants (institutions vs solo teachers), active vs churned.
- Platform-wide revenue (subscriptions + commission from marketplace sales).
- Growth trends (new signups over time), plan distribution.
- System health: storage usage, queue backlog, error rates.

### 14.2 Admin (Institution) Dashboard
- Total teachers/students, course completion rates institution-wide.
- Per-class and per-subject performance comparisons.
- Attendance trends.
- Internal billing/fee collection status (if applicable).

### 14.3 Teacher Dashboard
- Number of students per course, average quiz scores, assignment submission rates.
- Engagement metrics (video watch-time, lesson drop-off points).

### 14.4 Solo Teacher Dashboard
- Revenue over time, best-selling courses, refund rate.
- Student growth, course ratings, completion rates.
- Wallet balance and payout history.

### 14.5 Student Dashboard
- Personal progress across all enrolled courses.
- Upcoming deadlines and live sessions.
- Certificates earned and grade history.

---

## 15. UI/UX & DASHBOARD DESIGN PRINCIPLES

- Each role gets a **dedicated dashboard layout** tailored to their daily tasks (no generic one-size-fits-all screen).
- Consistent design system: shared component library (buttons, cards, tables, modals) across all role dashboards for visual consistency and faster development.
- Mobile-responsive from the start — many students and teachers will access the platform primarily via phone browsers.
- Clear visual hierarchy: most-used actions (e.g., "Create Course", "Take Quiz") always prominent and within one click from the dashboard home.
- Progress indicators everywhere (course completion bars, upload progress, quiz timers) to keep users informed of system state.
- Empty states and onboarding checklists to guide new Admins/Solo Teachers through initial setup (add first teacher, create first course, connect payment method).

---

## 16. THIRD-PARTY INTEGRATIONS

| Category | Providers |
|---|---|
| Payments (Global) | Stripe, PayPal |
| Payments (Local/East Africa) | M-Pesa, Tigo Pesa, Airtel Money, Pesapal, Flutterwave |
| Video Conferencing | Zoom API, Google Meet API |
| Email Delivery | Mailgun, Amazon SES, Postmark |
| SMS Gateway | Africa's Talking, Twilio |
| Cloud Storage | AWS S3, DigitalOcean Spaces |
| Search | Meilisearch, Algolia |
| Error Monitoring | Sentry, Bugsnag |
| Live Chat Support | Tawk.to, Crisp |
| Calendar Sync | Google Calendar API, Outlook Calendar API |

---

## 17. DEPLOYMENT & INFRASTRUCTURE ARCHITECTURE

**Typical Production Setup:**
- Load Balancer (Nginx) distributing traffic across multiple Laravel application servers.
- Separate Queue Worker servers (via Laravel Horizon/Supervisor) dedicated to heavy background jobs like video transcoding and bulk email sending.
- Redis server for caching, sessions, and queue management.
- MySQL/PostgreSQL primary database with read replicas for reporting-heavy queries.
- Object storage (S3-compatible) + CDN in front of it for fast global video/file delivery.
- Automated daily backups with off-site storage.
- Staging environment mirroring production for safe testing before releases.
- Containerization (Docker) for consistent environments across development, staging, and production.

---

## 18. TESTING STRATEGY

- **Unit Tests** — core business logic (grading calculations, plan limit enforcement, commission calculations) using Pest/PHPUnit.
- **Feature Tests** — full request/response cycles per role (e.g., "Teacher can create a quiz", "Student cannot access another tenant's course").
- **Integration Tests** — payment gateway webhooks, video processing pipeline.
- **Load Testing** — simulate concurrent video streaming and quiz submissions during peak exam periods.
- **Security Testing** — penetration testing focused on tenant isolation and payment flows before major releases.

---

## 19. RISK ASSESSMENT & MITIGATION

| Risk | Mitigation |
|---|---|
| Cross-tenant data leakage | Enforced global query scopes + automated tests specifically checking isolation |
| Video piracy/content theft | Signed URLs, watermarking, download restrictions, session limits |
| Payment gateway downtime | Support multiple gateways so one failure doesn't block all payments |
| Video processing bottlenecks during peak uploads | Dedicated, auto-scaling queue workers separate from the web servers |
| Plan limit disputes (billing complaints) | Transparent in-app usage meters showing current usage vs plan limit at all times |
| Solo Teacher content quality/fraud | Optional course moderation/approval workflow before public marketplace listing |

---

## 20. DEVELOPMENT ROADMAP

**Phase 1 — Foundation**
User roles, authentication, multi-tenancy setup, basic Admin/Super Admin panels, plan and subscription framework.

**Phase 2 — Core Learning Engine**
Course management, video upload/streaming pipeline, lesson structuring, drip content.

**Phase 3 — Assessment Engine**
Quizzes, assignments, grading, results, certificate generation and verification.

**Phase 4 — Monetization**
Full subscription billing, local + international payment gateways, Solo Teacher marketplace, wallet and payout system, coupons.

**Phase 5 — Engagement & Communication**
Notifications (in-app/email/SMS), forums, direct messaging, announcements, reviews/ratings.

**Phase 6 — Analytics & Polish**
Role-specific dashboards, exportable reports, gamification, live classes/webinars.

**Phase 7 — Scale & Mobile**
Full public API, mobile app support (iOS/Android via the API), performance optimization, advanced security (2FA, audit logs, load testing).

---

## 21. FUTURE EXPANSION IDEAS

- Native mobile apps (iOS/Android) consuming the existing API layer.
- AI-assisted quiz generation from uploaded lesson content.
- AI-powered plagiarism and essay-grading assistance.
- Affiliate/referral program for Solo Teachers to grow their student base.
- White-label option for large Enterprise institutions (their own branding/domain).
- Offline mode for mobile app (download lessons for offline viewing).
- Team/Organization add-on for Solo Teachers who want to bring on co-instructors.
- Integration marketplace allowing third-party plugins/extensions.

---

## 22. CONCLUSION

This architecture allows two very different customer types — **institutions** and **independent creators** — to coexist on one platform, each with billing, permissions, and workflows suited to their needs, while the Super Admin retains full oversight and control of the entire ecosystem. Laravel's mature package ecosystem (Cashier, Sanctum, Spatie Permission, Horizon, Scout) provides a strong, production-ready foundation to build this system securely and in a way that can scale from a small pilot to a large multi-country platform.

---

*This document is an architecture-level reference — it explains "WHAT" the system must do and "WHY," not "HOW" in code. It can be used directly as the foundation for a full Software Requirements Specification (SRS) or as a direct blueprint to begin development.*
