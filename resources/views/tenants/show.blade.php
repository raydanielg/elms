@extends('layouts.dashboard')

@section('page_title', $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-2xl">{{ strtoupper(substr($tenant->name, 0, 1)) }}</div>
            <div>
                <h2 class="text-2xl font-bold">{{ $tenant->name }}</h2>
                <p class="text-maroon-100 text-sm">{{ ucfirst($tenant->type) }} · {{ ucfirst($tenant->status) }}</p>
                <div class="flex gap-2 mt-2">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ $tenant->plan?->name ?? 'No Plan' }}</span>
                </div>
            </div>
            <a href="{{ route('tenants.edit', $tenant) }}" class="ml-auto px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl font-bold text-sm transition-all">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="font-semibold text-gray-700">{{ ucfirst($tenant->type) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="font-semibold text-gray-700">{{ ucfirst($tenant->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Plan</span><span class="font-semibold text-gray-700">{{ $tenant->plan?->name ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Domain</span><span class="font-semibold text-gray-700">{{ $tenant->domain ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Trial Ends</span><span class="font-semibold text-gray-700">{{ $tenant->trial_ends_at?->format('M d, Y') ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Created</span><span class="font-semibold text-gray-700">{{ $tenant->created_at->format('M d, Y') }}</span></div>
            </div>
        </div>
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Description</h3>
            <p class="text-sm text-gray-600">{{ $tenant->description ?? 'No description.' }}</p>
            <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                <div><p class="text-xs text-gray-400">Contact Email</p><p class="text-sm font-semibold text-gray-700">{{ $tenant->contact_email ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Contact Phone</p><p class="text-sm font-semibold text-gray-700">{{ $tenant->contact_phone ?? 'N/A' }}</p></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Users ({{ $tenant->users->count() }})</h3>
            <div class="space-y-2">
                @foreach($tenant->users->take(5) as $user)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="flex-1"><p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p><p class="text-xs text-gray-400">{{ $user->role_label }}</p></div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Courses ({{ $tenant->courses->count() }})</h3>
            <div class="space-y-2">
                @foreach($tenant->courses->take(5) as $course)
                <a href="{{ route('courses.show', $course) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-maroon-400 to-maroon-600 flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($course->title, 0, 1)) }}</div>
                    <p class="text-sm font-semibold text-gray-800 truncate flex-1">{{ $course->title }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
