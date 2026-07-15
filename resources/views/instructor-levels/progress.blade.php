@extends('layouts.dashboard')

@section('page_title', 'My Instructor Level')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Instructor Level</h2><p class="text-sm text-gray-500 mt-1">Track your progress and commission benefits</p></div>

    {{-- Current Level Card --}}
    @if($progress['current'])
    <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-8 text-white animate-slide-up">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-4xl">{{ $progress['current']->badge_icon ?? '🏆' }}</div>
            <div>
                <p class="text-sm text-maroon-100">Current Level</p>
                <h1 class="text-3xl font-extrabold">{{ $progress['current']->name }}</h1>
                <p class="text-sm text-maroon-200 mt-1">Commission Rate: {{ $progress['current']->commission_rate }}% · Payout: {{ $progress['current']->payout_speed_days }} days</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-xs text-maroon-200">Total Sales</p>
                <p class="text-2xl font-extrabold">{{ $progress['total_sales'] }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-xs text-maroon-200">Avg Rating</p>
                <p class="text-2xl font-extrabold">{{ $progress['avg_rating'] }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-xs text-maroon-200">Commission</p>
                <p class="text-2xl font-extrabold">{{ $progress['current']->commission_rate }}%</p>
            </div>
        </div>

        @if($progress['next'])
        <div class="mt-6">
            <div class="flex justify-between text-sm text-maroon-200 mb-2">
                <span>Progress to {{ $progress['next']->name }}</span>
                <span>{{ $progress['progress'] }}%</span>
            </div>
            <div class="w-full h-4 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-secondary-400 to-secondary-500 rounded-full transition-all duration-500" style="width: {{ $progress['progress'] }}%"></div>
            </div>
            <div class="mt-3 flex gap-4 text-sm">
                <p class="text-maroon-200">Sell <span class="font-bold text-white">{{ $progress['sales_remaining'] }}</span> more courses</p>
                @if($progress['rating_remaining'] > 0)<p class="text-maroon-200">Improve rating by <span class="font-bold text-white">{{ $progress['rating_remaining'] }}</span></p>@endif
            </div>
            <p class="text-sm text-maroon-200 mt-2">Next level commission: <span class="font-bold text-secondary-300">{{ $progress['next']->commission_rate }}%</span></p>
        </div>
        @else
        <div class="mt-6 bg-white/10 rounded-xl p-4 text-center">
            <p class="text-lg font-bold">Maximum level reached!</p>
            <p class="text-sm text-maroon-200">You're an Elite Instructor with the best commission rates.</p>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
        <p class="text-gray-400 font-semibold">No instructor level assigned yet</p>
        <p class="text-sm text-gray-300 mt-1">Start selling courses to begin your instructor journey!</p>
    </div>
    @endif

    {{-- Level History --}}
    @if($history->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Level History</h3></div>
        <div class="divide-y divide-gray-50">
            @foreach($history as $entry)
            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $entry->level->badge_icon ?? '🏆' }}</span>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $entry->level->name }}</p>
                        <p class="text-xs text-gray-400">{{ $entry->reason === 'manual_override' ? 'Manual override' : 'Auto-calculated' }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400">{{ $entry->created_at->format('M d, Y') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
