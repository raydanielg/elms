@extends('layouts.dashboard')

@section('page_title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Profile Header --}}
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-2xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-maroon-100 text-sm">{{ $user->email }}</p>
                <div class="flex gap-2 mt-2">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ $user->role_label }}</span>
                    @if($user->tenant)<span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ $user->tenant->name }}</span>@endif
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="ml-auto px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl font-bold text-sm transition-all">Edit Profile</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Stats --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">Statistics</h3>
                <div class="space-y-3 text-sm">
                    @if($user->isStudent())
                        <div class="flex justify-between"><span class="text-gray-400">Enrolled Courses</span><span class="font-bold text-gray-800">{{ $user->enrollments->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Completed</span><span class="font-bold text-success-600">{{ $user->enrollments->where('status', 'completed')->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Certificates</span><span class="font-bold text-orange-600">{{ $user->certificates->count() }}</span></div>
                    @elseif($user->hasRole(['teacher', 'solo_teacher']))
                        <div class="flex justify-between"><span class="text-gray-400">Created Courses</span><span class="font-bold text-gray-800">{{ $user->courses->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Total Students</span><span class="font-bold text-info-600">{{ $user->courses->sum('enrollment_count') }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-gray-400">Member Since</span><span class="font-bold text-gray-800">{{ $user->created_at->format('M Y') }}</span></div>
                </div>
            </div>
        </div>

        {{-- Bio --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">About</h3>
                <p class="text-sm text-gray-600">{{ $user->bio ?? 'No bio added yet.' }}</p>
                <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                    <div><p class="text-xs text-gray-400">Phone</p><p class="text-sm font-semibold text-gray-700">{{ $user->phone ?? 'N/A' }}</p></div>
                    <div><p class="text-xs text-gray-400">Email</p><p class="text-sm font-semibold text-gray-700">{{ $user->email }}</p></div>
                </div>
            </div>

            @if($user->isStudent() && $user->enrollments->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">Recent Courses</h3>
                <div class="space-y-2">
                    @foreach($user->enrollments->take(5) as $enrollment)
                    <a href="{{ route('courses.show', $enrollment->course) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-maroon-400 to-maroon-600 flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($enrollment->course->title, 0, 1)) }}</div>
                        <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-800 truncate">{{ $enrollment->course->title }}</p><p class="text-xs text-gray-400">{{ round($enrollment->progress) }}% complete</p></div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($user->certificates->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">Certificates</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($user->certificates as $cert)
                    <a href="{{ route('certificates.show', $cert) }}" class="flex items-center gap-3 p-3 bg-gradient-to-br from-maroon-50 to-orange-50 rounded-xl hover:scale-105 transition-all">
                        <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                        <div class="min-w-0"><p class="text-sm font-bold text-gray-800 truncate">{{ $cert->course->title }}</p><p class="text-xs text-gray-400">{{ $cert->created_at->format('M d, Y') }}</p></div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
