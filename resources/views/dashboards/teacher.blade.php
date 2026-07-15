@extends('layouts.dashboard')

@section('page_title', 'Teacher Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-maroon-100 text-sm">Your teaching dashboard — courses, students, and pending submissions.</p>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-xl text-sm font-bold transition-all hover:scale-105">+ New Course</a>
                <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-all">+ Announcement</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger">
        @php
            $kpis = [
                ['label' => 'My Courses', 'value' => $totalCourses, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13', 'gradient' => 'from-maroon-500 to-maroon-700', 'subtitle' => 'Created by you'],
                ['label' => 'Total Students', 'value' => $totalStudents, 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87', 'gradient' => 'from-info-400 to-info-600', 'subtitle' => 'Across all courses'],
                ['label' => 'Completion Rate', 'value' => $completionRate . '%', 'icon' => 'M9 12l2 2 4-4', 'gradient' => 'from-success-400 to-success-600', 'subtitle' => $totalEnrollments . ' enrollments'],
                ['label' => 'Pending Grading', 'value' => $pendingSubmissions, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'gradient' => 'from-warning-400 to-warning-600', 'subtitle' => 'Submissions to grade'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br {{ $kpi['gradient'] }} opacity-5 rounded-full -mr-8 -mt-8"></div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $kpi['gradient'] }} flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/></svg>
                </div>
                <p class="text-3xl font-extrabold text-gray-800">{{ $kpi['value'] }}</p>
                <p class="text-sm font-semibold text-gray-500 mt-1">{{ $kpi['label'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $kpi['subtitle'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Enrollment Trends (30 days)</h3>
            <div class="flex items-end gap-1 h-48">
                @foreach($enrollmentData as $idx => $val)
                    <div class="flex-1 flex flex-col items-center justify-end group">
                        <div class="w-full bg-gradient-to-t from-maroon-600 to-orange-400 rounded-t-md transition-all relative" style="height: {{ max(4, max(max($enrollmentData), 1) > 0 ? ($val / max(max($enrollmentData), 1)) * 100 : 4) }}%; min-height: 4px;">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-700 opacity-0 group-hover:opacity-100">{{ $val }}</span>
                        </div>
                        @if($idx % 5 === 0)<span class="text-[8px] text-gray-400 mt-1">{{ $trendLabels[$idx] ?? '' }}</span>@endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Course Enrollment</h3>
            <div class="space-y-4">
                @foreach($categoryLabels as $idx => $label)
                    @php $maxVal = max(max($categoryCounts), 1); $pct = round(($categoryCounts[$idx] / $maxVal) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="font-semibold text-gray-700 truncate max-w-[150px]">{{ $label }}</span>
                            <span class="font-bold text-gray-800">{{ $categoryCounts[$idx] }}</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if(empty($categoryLabels))
                    <p class="text-sm text-gray-400 text-center py-4">No courses yet</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Recent Students</h3></div>
        <div class="divide-y divide-gray-50">
            @forelse($recentUsers as $user)
                <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-info-400 to-info-600 flex items-center justify-center text-white font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">No students enrolled yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
