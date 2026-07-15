<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\QuizAttempt;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard($user);
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard($user);
        } elseif ($user->isSoloTeacher()) {
            return $this->soloTeacherDashboard($user);
        } else {
            return $this->studentDashboard($user);
        }
    }

    private function superAdminDashboard()
    {
        $totalTenants = Tenant::count();
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trialing')->count();
        $recentUsers = User::latest()->take(5)->get();
        $recentTenants = Tenant::latest()->take(5)->get();

        $trendLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $enrollmentData[] = Enrollment::whereDate('enrolled_at', $date)->count();
        }

        $categoryLabels = ['Institutions', 'Solo Teachers'];
        $categoryCounts = [
            Tenant::where('type', 'institution')->count(),
            Tenant::where('type', 'solo')->count(),
        ];

        return view('dashboards.super-admin', compact(
            'totalTenants', 'totalUsers', 'totalCourses', 'totalRevenue',
            'activeTenants', 'trialTenants', 'recentUsers', 'recentTenants',
            'trendLabels', 'enrollmentData', 'categoryLabels', 'categoryCounts'
        ));
    }

    private function adminDashboard($user)
    {
        $tenantId = $user->tenant_id;
        $totalTeachers = User::where('tenant_id', $tenantId)->where('role', 'teacher')->count();
        $totalStudents = User::where('tenant_id', $tenantId)->where('role', 'student')->count();
        $totalCourses = Course::where('tenant_id', $tenantId)->count();
        $totalEnrollments = Enrollment::whereHas('course', fn($q) => $q->where('tenant_id', $tenantId))->count();
        $completionRate = Enrollment::whereHas('course', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'completed')->count();
        $completionRate = $totalEnrollments > 0 ? round(($completionRate / $totalEnrollments) * 100) : 0;
        $recentUsers = User::where('tenant_id', $tenantId)->latest()->take(5)->get();
        $newUsers = User::where('tenant_id', $tenantId)->where('created_at', '>=', now()->subWeek())->count();

        $trendLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $enrollmentData[] = Enrollment::whereDate('enrolled_at', $date)
                ->whereHas('course', fn($q) => $q->where('tenant_id', $tenantId))->count();
        }

        $categoryLabels = ['Teachers', 'Students'];
        $categoryCounts = [$totalTeachers, $totalStudents];

        return view('dashboards.admin', compact(
            'totalTeachers', 'totalStudents', 'totalCourses', 'totalEnrollments',
            'completionRate', 'recentUsers', 'newUsers',
            'trendLabels', 'enrollmentData', 'categoryLabels', 'categoryCounts'
        ));
    }

    private function teacherDashboard($user)
    {
        $courses = Course::where('owner_id', $user->id)->withCount('enrollments')->get();
        $totalCourses = $courses->count();
        $totalStudents = $courses->sum('enrollments_count');
        $totalEnrollments = $totalStudents;
        $completionRate = Enrollment::whereIn('course_id', $courses->pluck('id'))
            ->where('status', 'completed')->count();
        $completionRate = $totalEnrollments > 0 ? round(($completionRate / $totalEnrollments) * 100) : 0;
        $pendingSubmissions = AssignmentSubmission::whereHas('assignment', fn($q) => $q->whereIn('course_id', $courses->pluck('id')))
            ->where('status', 'submitted')->count();
        $recentUsers = User::whereIn('id', Enrollment::whereIn('course_id', $courses->pluck('id'))->pluck('user_id'))
            ->latest()->take(5)->get();
        $newUsers = 0;

        $trendLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $enrollmentData[] = Enrollment::whereDate('enrolled_at', $date)
                ->whereIn('course_id', $courses->pluck('id'))->count();
        }

        $categoryLabels = $courses->pluck('title')->take(5)->toArray();
        $categoryCounts = $courses->pluck('enrollments_count')->take(5)->toArray();

        return view('dashboards.teacher', compact(
            'totalCourses', 'totalStudents', 'totalEnrollments', 'completionRate',
            'pendingSubmissions', 'recentUsers', 'newUsers',
            'trendLabels', 'enrollmentData', 'categoryLabels', 'categoryCounts'
        ));
    }

    private function soloTeacherDashboard($user)
    {
        $courses = Course::where('owner_id', $user->id)->withCount('enrollments')->get();
        $totalCourses = $courses->count();
        $totalStudents = $courses->sum('enrollments_count');
        $totalEnrollments = $totalStudents;
        $totalRevenue = Transaction::where('user_id', $user->id)->where('type', 'course_purchase')->where('status', 'completed')->sum('amount');
        $wallet = $user->wallet;
        $walletBalance = $wallet?->balance ?? 0;
        $completionRate = Enrollment::whereIn('course_id', $courses->pluck('id'))
            ->where('status', 'completed')->count();
        $completionRate = $totalEnrollments > 0 ? round(($completionRate / $totalEnrollments) * 100) : 0;
        $recentUsers = User::whereIn('id', Enrollment::whereIn('course_id', $courses->pluck('id'))->pluck('user_id'))
            ->latest()->take(5)->get();

        $trendLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $enrollmentData[] = Enrollment::whereDate('enrolled_at', $date)
                ->whereIn('course_id', $courses->pluck('id'))->count();
        }

        $categoryLabels = $courses->pluck('title')->take(5)->toArray();
        $categoryCounts = $courses->pluck('enrollments_count')->take(5)->toArray();

        return view('dashboards.solo-teacher', compact(
            'totalCourses', 'totalStudents', 'totalEnrollments', 'totalRevenue',
            'walletBalance', 'completionRate', 'recentUsers',
            'trendLabels', 'enrollmentData', 'categoryLabels', 'categoryCounts'
        ));
    }

    private function studentDashboard($user)
    {
        $enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();
        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->where('status', 'completed')->count();
        $inProgressCourses = $enrollments->where('status', 'active')->count();
        $certificates = Certificate::where('user_id', $user->id)->count();
        $avgProgress = $enrollments->count() > 0 ? round($enrollments->avg('progress')) : 0;
        $recentUsers = User::latest()->take(5)->get();
        $newUsers = 0;

        $trendLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $enrollmentData[] = QuizAttempt::where('user_id', $user->id)->whereDate('created_at', $date)->count();
        }

        $categoryLabels = $enrollments->pluck('course.title')->take(5)->toArray();
        $categoryCounts = $enrollments->pluck('progress')->map(fn($p) => (float)$p)->take(5)->toArray();

        return view('dashboards.student', compact(
            'totalCourses', 'completedCourses', 'inProgressCourses', 'certificates',
            'avgProgress', 'recentUsers', 'newUsers',
            'trendLabels', 'enrollmentData', 'categoryLabels', 'categoryCounts'
        ));
    }
}
