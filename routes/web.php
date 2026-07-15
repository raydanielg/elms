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
});
