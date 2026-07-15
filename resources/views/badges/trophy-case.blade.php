@extends('layouts.dashboard')

@section('page_title', 'My Trophy Case')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Trophy Case</h2><p class="text-sm text-gray-500 mt-1">Badges you've earned</p></div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 stagger">
        @forelse($badges as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center card-sm">
            <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center text-3xl mb-3" style="background: {{ $item->badge->color }}20">
                @if($item->badge->icon_image)<img src="{{ asset('storage/' . $item->badge->icon_image) }}" class="w-16 h-16 rounded-2xl object-cover">@else<span style="color: {{ $item->badge->color }}">{{ $item->badge->icon ?? '🏆' }}</span>@endif
            </div>
            <p class="font-bold text-gray-800 text-sm">{{ $item->badge->name }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ ucfirst($item->badge->category) }}</p>
            <p class="text-xs text-gray-300 mt-2">Earned {{ $item->created_at->format('M d, Y') }}</p>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            <p class="text-gray-400 font-semibold">No badges earned yet</p>
            <p class="text-xs text-gray-300 mt-1">Complete courses, pass quizzes, and stay active to earn badges!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
