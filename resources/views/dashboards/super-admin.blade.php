@extends('layouts.dashboard')

@section('page_title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 right-20 w-40 h-40 bg-orange-500/10 rounded-full -mb-20"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-maroon-100 text-sm">Platform-wide overview — tenants, revenue, growth, and system health at a glance.</p>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('tenants.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-xl text-sm font-bold transition-all hover:scale-105">+ New Tenant</a>
                <a href="{{ route('plans.create') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-all">+ New Plan</a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger">
        @php
            $kpis = [
                ['label' => 'Total Tenants', 'value' => $totalTenants, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'gradient' => 'from-maroon-500 to-maroon-700', 'subtitle' => $activeTenants . ' active · ' . $trialTenants . ' trialing'],
                ['label' => 'Total Users', 'value' => $totalUsers, 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0z', 'gradient' => 'from-info-400 to-info-600', 'subtitle' => 'Across all tenants'],
                ['label' => 'Total Courses', 'value' => $totalCourses, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253', 'gradient' => 'from-orange-400 to-orange-600', 'subtitle' => 'Published & draft'],
                ['label' => 'Total Revenue', 'value' => '$' . number_format($totalRevenue, 2), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1', 'gradient' => 'from-success-400 to-success-600', 'subtitle' => 'All transactions'],
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

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Enrollment Trend --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Platform Growth (30 days)</h3>
                <span class="text-xs font-semibold text-maroon-500 bg-maroon-50 px-3 py-1 rounded-full">Enrollments</span>
            </div>
            <div class="flex items-end gap-1 h-48">
                @foreach($enrollmentData as $idx => $val)
                    <div class="flex-1 flex flex-col items-center justify-end group">
                        <div class="w-full bg-gradient-to-t from-maroon-600 to-orange-400 rounded-t-md transition-all hover:from-maroon-700 hover:to-orange-500 relative" style="height: {{ max(4, ($val > 0 ? max($enrollmentData) : 1) > 0 ? ($val / max(max($enrollmentData), 1)) * 100 : 4) }}%; min-height: 4px;">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">{{ $val }}</span>
                        </div>
                        @if($idx % 5 === 0)
                            <span class="text-[8px] text-gray-400 mt-1 whitespace-nowrap">{{ $trendLabels[$idx] ?? '' }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tenant Distribution --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Tenant Distribution</h3>
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
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Total Tenants</span>
                    <span class="text-2xl font-extrabold text-maroon-600">{{ $totalTenants }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Tenants --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Recent Tenants</h3>
                <a href="{{ route('tenants.index') }}" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTenants as $tenant)
                    <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-maroon-400 to-maroon-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($tenant->type) }} · {{ ucfirst($tenant->status) }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $tenant->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No tenants yet</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Recent Users</h3>
                <a href="{{ route('users.index') }}" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentUsers as $user)
                    <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $user->role === 'super_admin' ? 'bg-maroon-100 text-maroon-700' : 'bg-gray-100 text-gray-600' }}">{{ $user->role_label }}</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No users yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- System Health --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">System Health</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 p-4 bg-success-50 rounded-xl">
                <div class="w-3 h-3 bg-success-500 rounded-full animate-pulse"></div>
                <div>
                    <p class="text-sm font-bold text-success-700">Server Status</p>
                    <p class="text-xs text-success-600">Operational</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-success-50 rounded-xl">
                <div class="w-3 h-3 bg-success-500 rounded-full animate-pulse"></div>
                <div>
                    <p class="text-sm font-bold text-success-700">Database</p>
                    <p class="text-xs text-success-600">Connected</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-warning-50 rounded-xl">
                <div class="w-3 h-3 bg-warning-500 rounded-full animate-pulse"></div>
                <div>
                    <p class="text-sm font-bold text-warning-700">Storage</p>
                    <p class="text-xs text-warning-600">42% used</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
