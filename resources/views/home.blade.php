@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . config('app.name', 'ELMS'))
@section('page_title', 'Dashboard')

@section('content')

{{-- Welcome --}}
<div class="mb-6 flex flex-row items-start sm:items-center justify-between gap-3 flex-wrap">
    <div class="min-w-0">
        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 tracking-tight">Hello {{ Auth::user()->name ?? 'User' }}</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Here's your {{ config('app.name', 'ELMS') }} overview for {{ now()->format('M d, Y') }}.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="#" class="px-2 sm:px-3 py-1.5 text-[11px] sm:text-xs font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span class="hidden sm:inline">Courses</span>
        </a>
        <a href="#" class="px-2 sm:px-3 py-1.5 text-[11px] sm:text-xs font-medium bg-maroon-500 text-white rounded-lg hover:bg-maroon-600 transition-colors inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">New Course</span>
        </a>
    </div>
</div>

{{-- KPI Stat Cards --}}
<div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4 mb-6">
    {{-- Total Users --}}
    <div class="card-sm bg-gradient-to-br from-maroon-600 to-maroon-700 rounded-xl border border-maroon-500 p-3 sm:p-5 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="flex items-start justify-between relative z-10">
            <span class="text-[10px] sm:text-xs font-medium text-maroon-100">Total Users</span>
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-maroon-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="mt-2 sm:mt-3 text-xl sm:text-3xl font-bold tracking-tight text-white relative z-10">{{ number_format($totalUsers ?? 0) }}</div>
        <div class="mt-1 text-[10px] sm:text-xs text-maroon-200 font-medium relative z-10">Registered users</div>
    </div>

    {{-- Active Courses --}}
    <div class="card-sm bg-gradient-to-br from-info-500 to-info-600 rounded-xl border border-info-400 p-3 sm:p-5 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="flex items-start justify-between relative z-10">
            <span class="text-[10px] sm:text-xs font-medium text-info-100">Active Courses</span>
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-info-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div class="mt-2 sm:mt-3 text-xl sm:text-3xl font-bold tracking-tight text-white relative z-10">{{ $totalCourses ?? 0 }}</div>
        <div class="mt-1 text-[10px] sm:text-xs text-info-100 font-medium relative z-10">Published courses</div>
    </div>

    {{-- Enrollments --}}
    <div class="card-sm bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl border border-orange-300 p-3 sm:p-5 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="flex items-start justify-between relative z-10">
            <span class="text-[10px] sm:text-xs font-medium text-orange-50">Enrollments</span>
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <div class="mt-2 sm:mt-3 text-xl sm:text-3xl font-bold tracking-tight text-white relative z-10">{{ number_format($totalEnrollments ?? 0) }}</div>
        <div class="mt-1 text-[10px] sm:text-xs text-orange-50 font-medium relative z-10">Active enrollments</div>
    </div>

    {{-- Completion Rate --}}
    <div class="card-sm bg-gradient-to-br from-success-500 to-success-600 rounded-xl border border-success-400 p-3 sm:p-5 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="flex items-start justify-between relative z-10">
            <span class="text-[10px] sm:text-xs font-medium text-success-100">Completion Rate</span>
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-success-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="mt-2 sm:mt-3 text-xl sm:text-3xl font-bold tracking-tight text-white relative z-10">{{ $completionRate ?? 0 }}%</div>
        <div class="mt-1 text-[10px] sm:text-xs text-success-100 font-medium relative z-10">Courses completed</div>
    </div>
</div>

{{-- Activity Overview --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-6">
    {{-- Enrollment Trend --}}
    <div class="bg-white rounded-xl border p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Enrollment Trend</h3>
                <p class="text-xs text-gray-400">Last 30 days</p>
            </div>
            <div class="flex items-center gap-3 text-[10px]">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-maroon-500"></span>Enrolled</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-success-400"></span>Completed</span>
            </div>
        </div>
        @php
            $enrollMax = max(array_merge($enrollmentData ?? [1], $completionTrendData ?? [0], [1]));
            $trendCount = count($trendLabels ?? []);
        @endphp
        <div class="flex items-end gap-[1px] h-48">
            @for($i = 0; $i < $trendCount; $i++)
                @php
                    $ePct = (($enrollmentData[$i] ?? 0) / $enrollMax) * 100;
                    $cPct = (($completionTrendData[$i] ?? 0) / $enrollMax) * 100;
                @endphp
                <div class="flex-1 flex flex-col items-center group cursor-pointer relative" style="min-width: 4px;">
                    <div class="w-full bg-gray-50 rounded-t-sm relative h-44 overflow-hidden flex flex-col justify-end">
                        @if($cPct > 0)
                            <div class="w-full bg-success-400" style="height: {{ max($cPct, 0) }}%"></div>
                        @endif
                        @if($ePct > 0)
                            <div class="w-full bg-maroon-500" style="height: {{ max($ePct, 0) }}%"></div>
                        @endif
                    </div>
                    @if($i % 5 === 0)
                        <span class="text-[8px] text-gray-400 font-medium mt-1 whitespace-nowrap">{{ $trendLabels[$i] }}</span>
                    @endif
                </div>
            @endfor
            @if($trendCount === 0)
                <div class="w-full flex items-center justify-center h-full text-xs text-gray-400">No enrollment data yet</div>
            @endif
        </div>
    </div>

    {{-- Course Categories --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Course Categories</h3>
            <p class="text-xs text-gray-400">By subject area</p>
        </div>
        @php $catMax = max($categoryCounts ?: [1]); @endphp
        <div class="space-y-3">
            @forelse($categoryLabels ?? [] as $i => $label)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-gray-700 truncate">{{ $label }}</span>
                        <span class="text-xs font-bold text-gray-900">{{ $categoryCounts[$i] ?? 0 }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-maroon-500 to-maroon-600 rounded-full transition-all duration-500" style="width: {{ (($categoryCounts[$i] ?? 0) / $catMax) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400 text-center py-8">No categories yet</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <h3 class="text-sm font-semibold text-gray-900">Action Center</h3>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="flex flex-col gap-1 p-3 rounded-xl border border-gray-100 hover:border-maroon-200 hover:bg-maroon-50/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">New Users</span>
                <span class="text-lg font-bold text-maroon-500">{{ $newUsers ?? 0 }}</span>
            </div>
            <span class="text-[10px] text-gray-400">This week</span>
        </div>
        <div class="flex flex-col gap-1 p-3 rounded-xl border border-gray-100 hover:border-maroon-200 hover:bg-maroon-50/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active Now</span>
                <span class="text-lg font-bold {{ ($activeNow ?? 0) > 0 ? 'text-info-500' : 'text-gray-300' }}">{{ $activeNow ?? 0 }}</span>
            </div>
            <span class="text-[10px] text-gray-400">Online today</span>
        </div>
        <div class="flex flex-col gap-1 p-3 rounded-xl border border-gray-100 hover:border-maroon-200 hover:bg-maroon-50/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Assessments</span>
                <span class="text-lg font-bold {{ ($pendingAssessments ?? 0) > 0 ? 'text-orange-500' : 'text-gray-300' }}">{{ $pendingAssessments ?? 0 }}</span>
            </div>
            <span class="text-[10px] text-gray-400">Pending grading</span>
        </div>
        <div class="flex flex-col gap-1 p-3 rounded-xl border border-gray-100 hover:border-maroon-200 hover:bg-maroon-50/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Certificates</span>
                <span class="text-lg font-bold {{ ($certificatesIssued ?? 0) > 0 ? 'text-success-500' : 'text-gray-300' }}">{{ $certificatesIssued ?? 0 }}</span>
            </div>
            <span class="text-[10px] text-gray-400">Issued this month</span>
        </div>
    </div>
</div>

{{-- Recent Users + System Info --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-6">
    {{-- System Health --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-900">System Health</h3>
            <p class="text-xs text-gray-400">Current status</p>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg bg-success-50 border border-success-100">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-gray-700">Server Status</span>
                </div>
                <span class="text-xs font-bold text-success-600">Online</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-maroon-50 border border-maroon-100">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-maroon-500"></span>
                    <span class="text-xs font-medium text-gray-700">Database</span>
                </div>
                <span class="text-xs font-bold text-maroon-600">Connected</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-orange-50 border border-orange-100">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span class="text-xs font-medium text-gray-700">Storage</span>
                </div>
                <span class="text-xs font-bold text-orange-600">Active</span>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="bg-white rounded-xl border p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Users</h3>
                <p class="text-xs text-gray-400">Latest registered users</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-3 py-2 font-medium">Name</th>
                        <th class="px-3 py-2 font-medium">Email</th>
                        <th class="px-3 py-2 font-medium">Joined</th>
                        <th class="px-3 py-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers ?? [] as $user)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-[10px]">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2.5 text-gray-500">{{ $user->email ?? 'N/A' }}</td>
                        <td class="px-3 py-2.5 text-gray-500">{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="px-3 py-2.5">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-success-50 text-success-700 border border-success-100">Verified</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-warning-50 text-warning-700 border border-warning-100">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-gray-400">
                            <p class="text-sm">No users yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="h-16 lg:hidden"></div>

@endsection
