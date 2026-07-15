@extends('layouts.dashboard')

@section('page_title', 'Institution Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-maroon-100 text-sm">Institution overview — teachers, students, courses, and completion rates.</p>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-xl text-sm font-bold transition-all hover:scale-105">+ New Course</a>
                <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-all">+ Announcement</a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger">
        @php
            $kpis = [
                ['label' => 'Teachers', 'value' => $totalTeachers, 'icon' => 'M12 14l9-5-9-5-9 5 9 5z', 'gradient' => 'from-maroon-500 to-maroon-700', 'subtitle' => 'Active staff'],
                ['label' => 'Students', 'value' => $totalStudents, 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87', 'gradient' => 'from-info-400 to-info-600', 'subtitle' => $newUsers . ' new this week'],
                ['label' => 'Courses', 'value' => $totalCourses, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13', 'gradient' => 'from-orange-400 to-orange-600', 'subtitle' => 'All statuses'],
                ['label' => 'Completion Rate', 'value' => $completionRate . '%', 'icon' => 'M9 12l2 2 4-4', 'gradient' => 'from-success-400 to-success-600', 'subtitle' => $totalEnrollments . ' enrollments'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br {{ $kpi['gradient'] }} opacity-5 rounded-full -mr-8 -mt-8"></div>
                <div class="flex items-start justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $kpi['gradient'] }} flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-gray-800">{{ $kpi['value'] }}</p>
                <p class="text-sm font-semibold text-gray-500 mt-1">{{ $kpi['label'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $kpi['subtitle'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Enrollment Trends (30 days)</h3>
                <span class="text-xs font-semibold text-maroon-500 bg-maroon-50 px-3 py-1 rounded-full">Daily</span>
            </div>
            <div class="flex items-end gap-1 h-48">
                @foreach($enrollmentData as $idx => $val)
                    <div class="flex-1 flex flex-col items-center justify-end group">
                        <div class="w-full bg-gradient-to-t from-maroon-600 to-orange-400 rounded-t-md transition-all hover:from-maroon-700 hover:to-orange-500 relative" style="height: {{ max(4, max(max($enrollmentData), 1) > 0 ? ($val / max(max($enrollmentData), 1)) * 100 : 4) }}%; min-height: 4px;">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ $val }}</span>
                        </div>
                        @if($idx % 5 === 0)<span class="text-[8px] text-gray-400 mt-1">{{ $trendLabels[$idx] ?? '' }}</span>@endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Users Breakdown</h3>
            <div class="space-y-4">
                @foreach($categoryLabels as $idx => $label)
                    @php $total = array_sum($categoryCounts); $pct = $total > 0 ? round(($categoryCounts[$idx] / $total) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="font-semibold text-gray-700">{{ $label }}</span>
                            <span class="font-bold text-gray-800">{{ $categoryCounts[$idx] }}</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 {{ $idx === 0 ? 'bg-gradient-to-r from-maroon-500 to-maroon-700' : 'bg-gradient-to-r from-orange-400 to-orange-600' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Recent Users</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">User</th>
                        <th class="px-5 py-3 text-left font-bold">Role</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-left font-bold">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentUsers as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3"><span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">{{ $user->role_label }}</span></td>
                            <td class="px-5 py-3"><span class="text-xs font-semibold px-2 py-1 rounded-full {{ $user->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">{{ ucfirst($user->status) }}</span></td>
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No users yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
