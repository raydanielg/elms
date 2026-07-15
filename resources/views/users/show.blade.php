@extends('layouts.dashboard')

@section('page_title', $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-maroon-100 text-sm">{{ $user->email }}</p>
                <div class="flex gap-2 mt-2">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ $user->role_label }}</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $user->status === 'active' ? 'bg-success-500' : 'bg-danger-500' }}">{{ ucfirst($user->status) }}</span>
                </div>
            </div>
            <a href="{{ route('users.edit', $user) }}" class="ml-auto px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl font-bold text-sm transition-all">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="font-semibold text-gray-700">{{ $user->phone ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Tenant</span><span class="font-semibold text-gray-700">{{ $user->tenant?->name ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Joined</span><span class="font-semibold text-gray-700">{{ $user->created_at->format('M d, Y') }}</span></div>
            </div>
        </div>
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Bio</h3>
            <p class="text-sm text-gray-600">{{ $user->bio ?? 'No bio added.' }}</p>
        </div>
    </div>
</div>
@endsection
