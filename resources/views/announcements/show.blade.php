@extends('layouts.dashboard')

@section('page_title', $announcement->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down"><a href="{{ route('announcements.index') }}" class="text-sm text-maroon-500 font-bold hover:text-maroon-700">← Back to Announcements</a></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-slide-up">
        @if($announcement->is_pinned)
        <span class="text-xs font-bold px-2 py-1 rounded-full bg-orange-100 text-orange-700 mb-3 inline-block">📌 Pinned</span>
        @endif
        <h2 class="text-2xl font-bold text-gray-800">{{ $announcement->title }}</h2>
        <div class="flex items-center gap-3 mt-3 text-sm text-gray-400">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($announcement->user->name, 0, 1)) }}</div>
                {{ $announcement->user->name }}
            </div>
            <span>·</span>
            <span>{{ $announcement->created_at->format('M d, Y \a\t H:i') }}</span>
        </div>
        <div class="mt-6 prose prose-sm max-w-none text-gray-600">{!! nl2br(e($announcement->body)) !!}</div>
    </div>
</div>
@endsection
