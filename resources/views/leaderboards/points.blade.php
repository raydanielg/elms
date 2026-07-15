@extends('layouts.dashboard')

@section('page_title', 'Points & Levels')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Points & Levels</h2><p class="text-sm text-gray-500 mt-1">Your XP progress and earning history</p></div>

    {{-- Level Progress Card --}}
    <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-6 text-white animate-slide-up">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-maroon-100">Current Level</p>
                <p class="text-2xl font-extrabold">{{ $progress['current_level']?->name ?? 'Beginner' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-maroon-100">Total XP</p>
                <p class="text-2xl font-extrabold">{{ number_format($total) }}</p>
            </div>
        </div>
        @if($progress['next_level'])
        <div>
            <div class="flex justify-between text-xs text-maroon-200 mb-1">
                <span>Progress to {{ $progress['next_level']->name }}</span>
                <span>{{ $progress['xp_remaining'] }} XP to go</span>
            </div>
            <div class="w-full h-3 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-secondary-400 to-secondary-500 rounded-full transition-all duration-500" style="width: {{ $progress['progress'] }}%"></div>
            </div>
        </div>
        @else
        <p class="text-sm text-maroon-200">Maximum level reached!</p>
        @endif
    </div>

    {{-- Points History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">XP Earning History</h3></div>
        <div class="divide-y divide-gray-50">
            @forelse($ledger as $entry)
            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $entry->action)) }}</p>
                    @if($entry->description)<p class="text-xs text-gray-400">{{ $entry->description }}</p>@endif
                    <p class="text-xs text-gray-300 mt-1">{{ $entry->created_at->format('M d, Y H:i') }}</p>
                </div>
                <p class="font-bold {{ $entry->points > 0 ? 'text-success-600' : 'text-danger-500' }}">{{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }} XP</p>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400">No XP earned yet. Start learning to earn points!</div>
            @endforelse
        </div>
    </div>
    <div class="flex justify-center">{{ $ledger->links() }}</div>
</div>
@endsection
