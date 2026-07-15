# ELMS — Instructor Monetization, Paid Courses, Levels & Commission System
## Part 4: Extended System Architecture Document

**Core Technology:** Laravel (PHP Framework)
**Document Version:** 1.0 — Companion to Parts 1, 2 & 3
**Focus:** How Teachers/Solo Teachers earn money ("kipato"), set up paid courses, progress through Instructor Levels, and how the Commission Engine works.

---

## TABLE OF CONTENTS

1. Introduction — The Instructor Income Engine
2. Instructor Types & Earning Eligibility
3. Setting Up Paid Courses (Full Flow)
4. Pricing Models Available to Instructors
5. Instructor Levels / Tier System (Deep Dive)
6. Commission Engine (Deep Dive)
7. Instructor Wallet & Earnings Dashboard
8. Payout System (Deep Dive)
9. Tax Handling & Statements
10. Institution-Employed Teacher Monetization (Special Case)
11. Promotions, Coupons & Instructor-Controlled Discounts
12. Anti-Abuse & Fraud Prevention
13. Instructor Growth Tools
14. Database Design (Conceptual Entities)
15. Analytics for Instructor Earnings
16. Notifications Tied to Earnings
17. Roadmap for This Module
18. Conclusion

---

## 1. INTRODUCTION — THE INSTRUCTOR INCOME ENGINE

One of the most powerful parts of ELMS is that **any qualified teacher or service provider can turn their knowledge into income** ("kipato") directly through the platform. Whether it's a Solo Teacher building a personal course business, or an Institution's Teacher who is allowed to sell supplementary paid content, ELMS provides a complete monetization pipeline: course pricing, tiered instructor levels, automatic commission calculation, a personal wallet, and a payout system.

This document explains, in full depth, how an instructor goes from "I want to teach and earn" to "I have money in my wallet and I can withdraw it" — and how the platform's **Level and Commission system** rewards top-performing instructors with better terms over time.

---

## 2. INSTRUCTOR TYPES & EARNING ELIGIBILITY

| Instructor Type | Can Sell Publicly? | Can Sell Internally (institution-only)? | Pays Platform Subscription? |
|---|---|---|---|
| Solo Teacher | Yes (Marketplace) | N/A | Yes, own Creator Plan |
| Institution Teacher (default) | No | Yes, if Admin enables it | No (Institution pays) |
| Institution Teacher (upgraded) | Yes, if Admin/Super Admin grants "Public Selling" permission | Yes | Institution pays base, Teacher may pay a small "Creator Add-on" |

**Key rule:** Earning eligibility is controlled by a simple toggle at both the platform level (Super Admin) and institution level (Admin), meaning an institution can decide whether their teachers are even allowed to monetize content directly, and under what revenue-share arrangement with the institution itself (see Section 10).

---

## 3. SETTING UP PAID COURSES (FULL FLOW)

1. **Eligibility Check** — system verifies the instructor's account type/plan allows course monetization (e.g., Solo Teacher on Starter plan may only publish 3 paid courses; Institution Teacher needs Admin approval toggle "ON").
2. **Course Creation** — instructor builds the course using the Course Builder (Part 1, Section 7.2; Part 2, Section 5).
3. **Pricing Setup** — instructor chooses a pricing model (Section 4) and sets the base price in their tenant's default currency.
4. **Preview & Submission** — instructor previews the public course landing page (title, description, thumbnail, preview video, syllabus).
5. **Optional Moderation Step** — if the platform/institution requires review before public listing (Part 2, Section 15), the course enters a `pending_review` state.
6. **Publish** — once approved (or immediately, if no moderation required), the course status changes to `published` and becomes purchasable.
7. **Sales Begin** — students discover and purchase via the Marketplace or a direct shareable course link.
8. **Revenue Flows to Wallet** — every successful sale automatically credits the instructor's Wallet, net of the applicable commission (Section 6).

---

## 4. PRICING MODELS AVAILABLE TO INSTRUCTORS

- **One-Time Payment** — a single fixed price unlocks the course permanently.
- **Subscription Access** — students pay monthly/annually for continued access to a course or a bundle of the instructor's courses ("All-Access Pass").
- **Free with Optional Paid Upgrade** — course is free, but a "Certificate," "1-on-1 mentorship add-on," or "downloadable resources pack" is sold separately.
- **Tiered Pricing** — early-bird discounted price for the first N enrollments, then price automatically increases (Part 2, Section 14).
- **Bundles** — multiple courses grouped at a combined discounted price.
- **Free (Lead Magnet)** — entirely free course used to build an audience and upsell paid courses (no revenue, but still tracked in analytics for funnel purposes).

Instructors configure all of this themselves through their dashboard — no developer or Super Admin involvement needed for day-to-day pricing changes.

---

## 5. INSTRUCTOR LEVELS / TIER SYSTEM (DEEP DIVE)

To reward consistency, quality, and growth, ELMS includes a **dynamic Instructor Level system** — similar to a loyalty program — where instructors climb tiers based on performance, and each tier unlocks better commission rates and platform perks.

### 5.1 Example Level Structure

| Level | Typical Requirement (configurable by Super Admin) | Commission Rate (Platform Cut) | Perks |
|---|---|---|---|
| **Level 1 — Starter** | New instructor (0–10 sales, or first 30 days) | 25% | Basic marketplace listing |
| **Level 2 — Rising** | 11–50 total sales AND average rating ≥ 4.0 | 20% | Slightly improved search ranking |
| **Level 3 — Established** | 51–200 total sales AND average rating ≥ 4.3 | 15% | "Rising Instructor" badge, featured rotation eligibility |
| **Level 4 — Pro** | 201–1000 total sales AND average rating ≥ 4.5 | 10% | Priority support, early access to new features |
| **Level 5 — Elite** | 1000+ total sales AND average rating ≥ 4.7 AND low refund rate | 7% | "Verified Elite Instructor" badge, homepage featured placement, dedicated payout processing (faster withdrawals) |

> All thresholds, names, and commission percentages above are **fully configurable** by Super Admin through the Dynamic Plan/Level Builder (Part 2, Section 2.3) — the table above is an illustrative default, not a hardcoded rule.

### 5.2 Level Calculation Factors
The system automatically recalculates an instructor's level periodically (e.g., nightly job) based on a weighted combination of:
- **Total lifetime sales / total revenue generated.**
- **Average course rating** across all their published courses.
- **Refund rate** (high refund rates can prevent level advancement even with high sales).
- **Completion rate of their students** (an instructor whose students actually finish courses is rewarded more than one with high sales but poor engagement).
- **Response time / support quality** (optional factor — e.g., how quickly they respond to student questions in the forum).

### 5.3 Level Progression & Demotion Rules
- Levels are generally **earned upward automatically** once thresholds are met.
- Optional **demotion protection window** — an instructor doesn't instantly drop a level after one bad month; the system uses a rolling average (e.g., trailing 90 days) to avoid punishing a single bad review or a temporary dip.
- Super Admin can manually override an instructor's level in special cases (e.g., promotional boost for a new but promising instructor, or manual demotion for policy violations).

### 5.4 Level Visibility
- Instructors see their current level, current commission rate, and exactly what's needed to reach the next level (a clear progress bar: "Sell 12 more courses and maintain a 4.3+ rating to reach Level 3").
- This transparency is a strong motivational tool — instructors can see a direct, tangible benefit (lower commission = more take-home pay) for improving quality and sales.

---

## 6. COMMISSION ENGINE (DEEP DIVE)

### 6.1 How Commission Is Calculated
For every course sale:
```
Instructor Payout = Sale Price − Platform Commission (based on current Level) − Payment Gateway Fee (if passed through) − Applicable Taxes (if configured)
```

- The **Commission Engine** looks up the instructor's current Level at the moment of sale (not retroactively changed if their level changes later) — this ensures fair, predictable accounting per transaction.
- Commission can also vary by **plan type**, independent of level (e.g., a "Pro Creator" plan subscriber might get an automatic 5% reduction on top of their level-based rate, since they already pay a higher monthly platform fee).
- Institutions may have a **separate internal commission arrangement** with their own Teachers (see Section 10) — this is configured per institution, independent of the public Marketplace commission engine.

### 6.2 Commission Transparency
- Every transaction in the instructor's Wallet ledger shows a full breakdown: Sale Price → Commission Deducted → Gateway Fee (if applicable) → Net Amount Credited.
- Nothing is a "black box" — instructors can always see exactly why they received a specific net amount for a specific sale.

### 6.3 Special Commission Rules
- **First-Sale Bonus** — optionally, the platform can offer 0% commission on an instructor's first 1–3 sales to encourage new instructors to keep going.
- **Referral-Driven Sales** — if a sale came through another user's affiliate link (Part 2, Section 17), an additional affiliate commission is deducted *before* calculating the instructor's net (three-way split: Platform, Affiliate, Instructor).
- **Bundle Sales Split** — when a bundle contains courses from multiple instructors (future feature), revenue is split proportionally based on each course's individual price weight within the bundle, then each instructor's own commission rate is applied to their portion.

---

## 7. INSTRUCTOR WALLET & EARNINGS DASHBOARD

The **Wallet** is the financial home base for every earning instructor:

- **Available Balance** — funds cleared and ready for withdrawal.
- **Pending Balance** — funds from recent sales still within the platform's refund window (e.g., 7 days) before being marked available.
- **Lifetime Earnings** — total historical revenue earned (gross and net).
- **Transaction Ledger** — a full, filterable, exportable history of every sale, commission deduction, refund, and payout.
- **Earnings Breakdown Charts** — revenue by course, by month, by pricing model (one-time vs subscription vs bundle).
- **Next Level Progress Widget** — visual tracker showing progress toward the next Instructor Level and what it would mean for their commission rate.

---

## 8. PAYOUT SYSTEM (DEEP DIVE)

### 8.1 Withdrawal Flow
1. Instructor requests a withdrawal from their **Available Balance** (must meet a configurable minimum threshold, e.g., 20,000 TZS / $10).
2. Instructor selects a payout method: Mobile Money (M-Pesa/Tigo Pesa/Airtel Money), Bank Transfer, or (for higher levels) faster/priority payout channels.
3. Request enters the `withdrawals` table with status `pending`.
4. Depending on configuration (Part 2, Section 3.4):
   - **Manual mode** — Super Admin/Finance team reviews and processes, then marks `completed`.
   - **Automated mode** — system calls the relevant payment gateway's disbursement API directly.
5. Instructor receives a notification (in-app + email + SMS) once the payout is completed, along with a downloadable payout receipt.

### 8.2 Payout Speed by Level
- Higher Instructor Levels can unlock **faster payout cycles** as a perk:
  - Starter/Rising: standard 5–7 business day processing.
  - Established/Pro: 2–3 business day processing.
  - Elite: near-instant/24-hour priority processing.
- This is a strong incentive layer tied directly into the Level system from Section 5.

### 8.3 Payout Limits & Controls
- Configurable maximum withdrawal amount per request/per day (fraud control).
- Optional identity verification (KYC) requirement before an instructor can request their first payout above a certain cumulative threshold.
- Multi-currency payout support where the gateway allows it, with clear display of any conversion rate applied.

---

## 9. TAX HANDLING & STATEMENTS

- Instructors can download **Monthly/Annual Earnings Statements** (PDF) summarizing gross sales, commissions paid, and net income — useful for their own personal tax filing.
- Institutions/Super Admin can configure whether **VAT/Withholding Tax** should be automatically deducted at the point of sale for certain regions, with the deducted amount clearly itemized.
- All statements and transaction records are retained long-term for compliance and dispute resolution.

---

## 10. INSTITUTION-EMPLOYED TEACHER MONETIZATION (SPECIAL CASE)

When an Admin enables monetization for their own Teachers (rather than only Solo Teachers selling on the open Marketplace), a slightly different flow applies:

- **Internal Revenue Share Configuration** — Admin sets what percentage of a Teacher's course sales the institution keeps versus what the Teacher personally receives (e.g., Institution keeps 40%, Teacher receives 60% of the post-platform-commission amount).
- **Internal-Only vs Public Listing Choice** — Admin decides whether a Teacher's paid course is sold only to the institution's own students (e.g., an optional exam-prep add-on) or also listed publicly on the open Marketplace.
- **Separate Wallets** — the Institution has its own Wallet (receiving its share), and the Teacher has their own personal Wallet (receiving their share), both governed by the same Commission Engine and Payout System described above.
- This allows institutions to build **internal incentive programs**, rewarding their best teachers with direct extra income for producing high-quality, in-demand supplementary content.

---

## 11. PROMOTIONS, COUPONS & INSTRUCTOR-CONTROLLED DISCOUNTS

- Instructors can create their own **coupon codes** (percentage or fixed discount, usage limits, expiry dates) to run personal promotions (e.g., a launch discount, a social-media-exclusive code).
- Instructors can opt into **platform-wide sales events** (e.g., a "Back to School Sale" run by Super Admin across the whole Marketplace) — participation is optional per instructor, since a platform-wide discount affects their net earnings.
- **Flash Sale Scheduler** — instructors can schedule a temporary price drop for a set window (e.g., 48-hour sale) without manually changing and reverting the price.

---

## 12. ANTI-ABUSE & FRAUD PREVENTION

- **Refund Abuse Detection** — flags instructors or students with unusually high refund rates for manual review.
- **Self-Purchase Detection** — flags suspicious patterns where an instructor may be using secondary accounts to inflate their own sales numbers (important since sales volume affects Level progression).
- **Payout Hold on Disputed Transactions** — funds tied to a payment currently under a gateway dispute/chargeback are held out of the Available Balance until resolved.
- **Rating Manipulation Detection** — flags unusual clusters of 5-star reviews created in a short time window from new/unverified accounts.

---

## 13. INSTRUCTOR GROWTH TOOLS

- **Sales Funnel Analytics** — course page views → add-to-cart/enrollment click → completed purchase, helping instructors see where potential buyers drop off.
- **A/B Testing for Course Thumbnails/Titles** (optional advanced feature) — test two versions of a course landing page and let the system automatically favor the better-converting one.
- **Instructor Academy** — a built-in help/resource area (articles, short courses) teaching instructors how to price, market, and improve their own courses — directly increasing platform-wide course quality and sales.

---

## 14. DATABASE DESIGN (CONCEPTUAL ENTITIES)

- `instructor_levels` — level definitions (`name`, `min_sales`, `min_rating`, `commission_rate`, `perks_json`), fully editable by Super Admin.
- `instructor_level_history` — logs every level change per instructor with date and reason (auto-calculated or manual override).
- `courses` — includes `price`, `pricing_model` (one-time/subscription/bundle), `instructor_id`.
- `transactions` — includes `gross_amount`, `commission_amount`, `commission_rate_applied`, `net_amount`, `status`.
- `wallets` — `available_balance`, `pending_balance`, `lifetime_earnings`, owner (`instructor_id` or `institution_id`).
- `withdrawals` — `amount`, `method`, `status`, `processed_at`.
- `revenue_shares` — institution-teacher internal split configuration (percentage rules).
- `coupons` — instructor-owned discount codes with scope restrictions.
- `refunds` — linked to `transactions`, tracked for both financial reversal and fraud-pattern analysis.

**Key Relationships:**
- An `Instructor` **hasOne** `Wallet` and **hasMany** `Transactions`, `Withdrawals`, and `InstructorLevelHistory` entries.
- A `Transaction` **belongsTo** a `Course` and an `Instructor`, and optionally references an `AffiliateReferral` and a `Coupon`.
- An `Institution` **hasMany** `RevenueShares` governing its own Teachers' internal payout splits.

---

## 15. ANALYTICS FOR INSTRUCTOR EARNINGS

- **Platform-Wide Revenue Report (Super Admin)** — total commission earned by the platform, broken down by instructor level tier, plan type, and time period.
- **Instructor Leaderboard by Earnings** (optional, opt-in for privacy) — top-earning instructors, useful for Super Admin to identify star performers worth featuring.
- **Level Distribution Report** — how many instructors sit at each level, helping Super Admin fine-tune level thresholds over time (e.g., if almost everyone reaches Elite too easily, thresholds may need adjusting).
- **Churn Risk Indicators** — instructors whose sales have dropped significantly month-over-month, flagged for potential re-engagement outreach (e.g., a personalized email offering marketing tips).

---

## 16. NOTIFICATIONS TIED TO EARNINGS

- "You made a sale! [Course Name] was purchased by a new student." (in-app + email)
- "Your withdrawal of [Amount] has been processed." (in-app + email + SMS)
- "Congratulations! You've been promoted to Level 4 — Pro Instructor. Your commission rate is now 10%." (in-app + email)
- "Your course [Course Name] received a new 5-star review!" (in-app)
- "Your refund rate has increased — review your course quality to protect your Instructor Level." (in-app + email, sent privately/constructively)

---

## 17. ROADMAP FOR THIS MODULE

**Stage 1 — Core Monetization**
Paid course setup, one-time pricing, basic Wallet, manual payouts.

**Stage 2 — Commission Engine**
Configurable commission rates, full transaction ledger transparency.

**Stage 3 — Instructor Levels**
Automatic level calculation, level-based commission rates, progress tracking UI.

**Stage 4 — Advanced Payouts**
Automated payout API integration, level-based payout speed perks, KYC for large withdrawals.

**Stage 5 — Institution Revenue Sharing**
Internal Teacher monetization with configurable institution/teacher splits.

**Stage 6 — Growth & Fraud Tools**
Sales funnel analytics, A/B testing, fraud/anti-abuse detection systems.

---

## 18. CONCLUSION

This module turns ELMS into a genuine **income-generating platform** for teachers and independent service providers — not just a content delivery tool. By combining flexible paid-course setup, a transparent Commission Engine, and a motivating Instructor Level/Tier system that rewards quality and consistency with real financial benefits (lower commissions, faster payouts, better visibility), ELMS creates a self-reinforcing cycle: better instructors earn more and get promoted, which pushes them to keep improving, which keeps students happy — driving growth for the whole platform.

---

*This document should be read together with "ELMS_Architecture.md" (Part 1), "ELMS_Advanced_Features_and_Integrations.md" (Part 2), and "ELMS_Awards_Certificates_Recognition.md" (Part 3). Together, all four form the complete architectural reference for building ELMS on Laravel.*
