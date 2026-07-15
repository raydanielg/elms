<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\FeatureFlagController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\WebhookEndpointController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CourseApprovalController;
use App\Http\Controllers\CourseVersionController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\TranscriptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Marketplace (public)
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{course}', [MarketplaceController::class, 'show'])->name('marketplace.show');

// Certificate verification (public)
Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Transcript verification (public)
Route::get('/transcripts/verify/{code}', [TranscriptController::class, 'verify'])->name('transcripts.verify');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Courses
    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');

    // Course Modules
    Route::post('courses/{course}/modules', [CourseModuleController::class, 'store'])->name('courses.modules.store');
    Route::put('courses/{course}/modules/{module}', [CourseModuleController::class, 'update'])->name('courses.modules.update');
    Route::delete('courses/{course}/modules/{module}', [CourseModuleController::class, 'destroy'])->name('courses.modules.destroy');

    // Lessons
    Route::post('modules/{module}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::put('modules/{module}/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('modules/{module}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::get('courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('courses.lessons.show');
    Route::post('courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'markComplete'])->name('courses.lessons.complete');

    // Quizzes
    Route::get('courses/{course}/quizzes', [QuizController::class, 'index'])->name('courses.quizzes.index');
    Route::get('courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('courses.quizzes.create');
    Route::post('courses/{course}/quizzes', [QuizController::class, 'store'])->name('courses.quizzes.store');
    Route::get('courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('courses.quizzes.show');
    Route::get('courses/{course}/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('courses.quizzes.edit');
    Route::put('courses/{course}/quizzes/{quiz}', [QuizController::class, 'update'])->name('courses.quizzes.update');
    Route::delete('courses/{course}/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('courses.quizzes.destroy');
    Route::post('courses/{course}/quizzes/{quiz}/questions', [QuizController::class, 'addQuestion'])->name('courses.quizzes.questions.store');
    Route::delete('courses/{course}/quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('courses.quizzes.questions.destroy');
    Route::get('courses/{course}/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('courses.quizzes.start');
    Route::post('courses/{course}/quizzes/{quiz}/attempts/{attempt}/submit', [QuizController::class, 'submit'])->name('courses.quizzes.submit');
    Route::get('courses/{course}/quizzes/{quiz}/attempts/{attempt}/result', [QuizController::class, 'result'])->name('courses.quizzes.result');

    // Assignments
    Route::get('courses/{course}/assignments', [AssignmentController::class, 'index'])->name('courses.assignments.index');
    Route::get('courses/{course}/assignments/create', [AssignmentController::class, 'create'])->name('courses.assignments.create');
    Route::post('courses/{course}/assignments', [AssignmentController::class, 'store'])->name('courses.assignments.store');
    Route::get('courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('courses.assignments.show');
    Route::get('courses/{course}/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('courses.assignments.edit');
    Route::put('courses/{course}/assignments/{assignment}', [AssignmentController::class, 'update'])->name('courses.assignments.update');
    Route::delete('courses/{course}/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('courses.assignments.destroy');
    Route::post('courses/{course}/assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('courses.assignments.submit');
    Route::post('courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [AssignmentController::class, 'grade'])->name('courses.assignments.grade');

    // Enrollments
    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Certificates
    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::post('courses/{course}/certificate', [CertificateController::class, 'generate'])->name('certificates.generate');

    // Reviews
    Route::post('courses/{course}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('courses/{course}/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Wallet
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet/withdraw', [WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::resource('plans', PlanController::class)->except(['show']);
        Route::resource('users', UserController::class);
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
    });

    // Advanced Features

    // Payment Webhook (no auth - external callbacks)
    Route::post('/api/v1/payments/webhook/{gateway}', [PaymentGatewayController::class, 'handleWebhook'])->name('api.payments.webhook');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{course}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('/referrals/{course}/generate', [ReferralController::class, 'generateLink'])->name('referrals.generate');

    // Coupons
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon'])->name('coupons.validate');

    // Course Versions
    Route::get('/courses/{course}/versions', [CourseVersionController::class, 'index'])->name('course-versions.index');
    Route::post('/courses/{course}/versions/create', [CourseVersionController::class, 'createVersion'])->name('course-versions.create');
    Route::post('/courses/{course}/versions/{version}/publish', [CourseVersionController::class, 'publish'])->name('course-versions.publish');
    Route::get('/courses/{course}/versions/{version}', [CourseVersionController::class, 'show'])->name('course-versions.show');

    // Course Approvals
    Route::post('/courses/{course}/request-approval', [CourseApprovalController::class, 'requestApproval'])->name('course-approvals.request');
    Route::post('/course-approvals/{approval}/approve', [CourseApprovalController::class, 'approve'])->name('course-approvals.approve');
    Route::post('/course-approvals/{approval}/reject', [CourseApprovalController::class, 'reject'])->name('course-approvals.reject');

    // Theme
    Route::get('/theme', [ThemeController::class, 'edit'])->name('theme.edit');
    Route::put('/theme', [ThemeController::class, 'update'])->name('theme.update');

    // SMS
    Route::get('/sms/gateways', [SmsController::class, 'gateways'])->name('sms.gateways');
    Route::post('/sms/gateways', [SmsController::class, 'storeGateway'])->name('sms.gateways.store');
    Route::put('/sms/gateways/{gateway}', [SmsController::class, 'updateGateway'])->name('sms.gateways.update');
    Route::get('/sms/templates', [SmsController::class, 'templates'])->name('sms.templates');
    Route::put('/sms/templates/{template}', [SmsController::class, 'updateTemplate'])->name('sms.templates.update');
    Route::get('/sms/campaigns', [SmsController::class, 'campaigns'])->name('sms.campaigns');
    Route::get('/sms/campaigns/create', [SmsController::class, 'createCampaign'])->name('sms.campaigns.create');
    Route::post('/sms/campaigns', [SmsController::class, 'storeCampaign'])->name('sms.campaigns.store');
    Route::get('/sms/credits', [SmsController::class, 'credits'])->name('sms.credits');
    Route::post('/sms/credits/purchase', [SmsController::class, 'purchaseCredits'])->name('sms.credits.purchase');

    // Admin-only advanced features
    Route::middleware('role:super_admin|admin')->group(function () {
        Route::get('/feature-flags', [FeatureFlagController::class, 'index'])->name('feature-flags.index');
        Route::put('/feature-flags/{flag}', [FeatureFlagController::class, 'update'])->name('feature-flags.update');
        Route::post('/feature-flags/{flag}/toggle', [FeatureFlagController::class, 'toggleGlobal'])->name('feature-flags.toggle');

        Route::get('/payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
        Route::post('/payment-gateways', [PaymentGatewayController::class, 'store'])->name('payment-gateways.store');
        Route::put('/payment-gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
        Route::delete('/payment-gateways/{gateway}', [PaymentGatewayController::class, 'destroy'])->name('payment-gateways.destroy');
        Route::get('/payment-gateways/logs', [PaymentGatewayController::class, 'webhookLogs'])->name('payment-gateways.logs');

        Route::resource('currencies', CurrencyController::class)->except(['create', 'show', 'edit']);
        Route::resource('coupons', CouponController::class)->except(['create', 'show', 'edit']);
        Route::resource('custom-fields', CustomFieldController::class)->except(['create', 'show', 'edit']);
        Route::resource('automation-rules', AutomationRuleController::class)->except(['create', 'show', 'edit']);
        Route::resource('notification-templates', NotificationTemplateController::class)->except(['create', 'show', 'edit']);
        Route::put('/notification-triggers/{event}', [NotificationTemplateController::class, 'updateTrigger'])->name('notification-triggers.update');
        Route::get('/course-approvals', [CourseApprovalController::class, 'index'])->name('course-approvals.index');

        Route::resource('webhooks', WebhookEndpointController::class)->except(['create', 'show', 'edit']);
        Route::get('/webhooks/logs', [WebhookEndpointController::class, 'logs'])->name('webhooks.logs');

        Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
        Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
        Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    });

    // Recognition Engine — Certificates, Badges, Awards, Leaderboards, Points, Transcripts

    // Certificates (extend existing)
    Route::post('/certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('certificates.revoke');

    // Certificate Templates
    Route::get('/certificate-templates', [CertificateTemplateController::class, 'index'])->name('certificate-templates.index');
    Route::post('/certificate-templates', [CertificateTemplateController::class, 'store'])->name('certificate-templates.store');
    Route::put('/certificate-templates/{template}', [CertificateTemplateController::class, 'update'])->name('certificate-templates.update');
    Route::delete('/certificate-templates/{template}', [CertificateTemplateController::class, 'destroy'])->name('certificate-templates.destroy');

    // Badges
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');
    Route::post('/badges', [BadgeController::class, 'store'])->name('badges.store');
    Route::put('/badges/{badge}', [BadgeController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [BadgeController::class, 'destroy'])->name('badges.destroy');
    Route::post('/badges/{badge}/rules', [BadgeController::class, 'storeRule'])->name('badges.rules.store');
    Route::delete('/badges/{badge}/rules/{rule}', [BadgeController::class, 'destroyRule'])->name('badges.rules.destroy');
    Route::post('/badges/award', [BadgeController::class, 'awardManual'])->name('badges.award');
    Route::get('/trophy-case', [BadgeController::class, 'trophyCase'])->name('badges.trophy-case');

    // Awards
    Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');
    Route::post('/awards', [AwardController::class, 'store'])->name('awards.store');
    Route::delete('/awards/{award}', [AwardController::class, 'destroy'])->name('awards.destroy');
    Route::get('/honor-roll', [AwardController::class, 'honorRoll'])->name('awards.honor-roll');

    // Leaderboards
    Route::get('/leaderboards/tenant', [LeaderboardController::class, 'tenantLeaderboard'])->name('leaderboards.tenant');
    Route::get('/courses/{course}/leaderboard', [LeaderboardController::class, 'courseLeaderboard'])->name('leaderboards.course');
    Route::get('/points', [LeaderboardController::class, 'pointsHistory'])->name('points.history');

    // Transcripts
    Route::get('/transcripts', [TranscriptController::class, 'index'])->name('transcripts.index');
    Route::post('/transcripts/generate', [TranscriptController::class, 'generate'])->name('transcripts.generate');
    Route::get('/transcripts/{transcript}', [TranscriptController::class, 'show'])->name('transcripts.show');
    Route::delete('/transcripts/{transcript}', [TranscriptController::class, 'destroy'])->name('transcripts.destroy');
});
