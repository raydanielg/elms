<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', now()->subWeek())->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('home', [
            'totalUsers' => $totalUsers,
            'totalCourses' => 0,
            'totalEnrollments' => 0,
            'completionRate' => 0,
            'newUsers' => $newUsers,
            'activeNow' => 0,
            'pendingAssessments' => 0,
            'certificatesIssued' => 0,
            'recentUsers' => $recentUsers,
            'trendLabels' => [],
            'enrollmentData' => [],
            'completionTrendData' => [],
            'categoryLabels' => [],
            'categoryCounts' => [],
        ]);
    }
}
