@extends('layouts.dashboard')

@section('page_title', 'Honor Roll')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Honor Roll</h2><p class="text-sm text-gray-500 mt-1">Celebrating our outstanding students</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($awards as $award)
        <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-6 text-white card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full bg-white/20">{{ ucfirst(str_replace('_', ' ', $award->type)) }}</span>
            </div>
            <h3 class="font-bold text-lg">{{ $award->title }}</h3>
            <p class="text-sm text-maroon-100 mt-1">{{ $award->recipient->name }}</p>
            @if($award->description)<p class="text-xs text-maroon-200 mt-2">{{ $award->description }}</p>@endif
            <p class="text-xs text-maroon-300 mt-3">{{ $award->period ?? $award->created_at->format('M d, Y') }}</p>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No public awards yet</p></div>
        @endforelse
    </div>
</div>
@endsection
