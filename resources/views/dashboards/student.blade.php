@extends('layouts.dashboard')

@section('page_title', 'Student Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-maroon-100 text-sm">Your learning journey — courses, progress, certificates, and upcoming deadlines.</p>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('marketplace.index') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-xl text-sm font-bold transition-all hover:scale-105">Browse Courses</a>
                <a href="{{ route('enrollments.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-all">My Enrollments</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger">
        @php
            $kpis = [
                ['label' => 'Enrolled Courses', 'value' => $totalCourses, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13', 'gradient' => 'from-maroon-500 to-maroon-700', 'subtitle' => $inProgressCourses . ' in progress'],
                ['label' => 'Completed', 'value' => $completedCourses, 'icon' => 'M9 12l2 2 4-4', 'gradient' => 'from-success-400 to-success-600', 'subtitle' => 'Courses finished'],
                ['label' => 'Certificates', 'value' => $certificates, 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806', 'gradient' => 'from-orange-400 to-orange-600', 'subtitle' => 'Earned'],
                ['label' => 'Avg Progress', 'value' => $avgProgress . '%', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'gradient' => 'from-info-400 to-info-600', 'subtitle' => 'Across all courses'],
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
            <h3 class="font-bold text-gray-800 mb-4">Activity (30 days)</h3>
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
            <h3 class="font-bold text-gray-800 mb-4">Course Progress</h3>
            <div class="space-y-4">
                @foreach($categoryLabels as $idx => $label)
                    @php $pct = round($categoryCounts[$idx]); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="font-semibold text-gray-700 truncate max-w-[150px]">{{ $label }}</span>
                            <span class="font-bold text-gray-800">{{ $pct }}%</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-success-400 to-success-600 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if(empty($categoryLabels))
                    <p class="text-sm text-gray-400 text-center py-4">Not enrolled in any course yet</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('marketplace.index') }}" class="flex flex-col items-center gap-2 p-5 bg-maroon-50 hover:bg-maroon-100 rounded-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                <span class="text-sm font-bold text-maroon-700">Browse Marketplace</span>
            </a>
            <a href="{{ route('enrollments.index') }}" class="flex flex-col items-center gap-2 p-5 bg-info-50 hover:bg-info-100 rounded-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                <span class="text-sm font-bold text-info-700">My Enrollments</span>
            </a>
            <a href="{{ route('certificates.index') }}" class="flex flex-col items-center gap-2 p-5 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806"/></svg>
                <span class="text-sm font-bold text-orange-700">Certificates</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="flex flex-col items-center gap-2 p-5 bg-success-50 hover:bg-success-100 rounded-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659"/></svg>
                <span class="text-sm font-bold text-success-700">Notifications</span>
            </a>
        </div>
    </div>
</div>
@endsection
