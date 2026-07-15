@extends('layouts.dashboard')

@section('page_title', 'Announcements')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Announcements</h2><p class="text-sm text-gray-500 mt-1">Latest news and updates</p></div>
        @if(auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher']))
        <a href="{{ route('announcements.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Announcement</a>
        @endif
    </div>

    <div class="space-y-4 stagger">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3 flex-1">
                        @if($announcement->is_pinned)
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2h2a1 1 0 011 1v2a1 1 0 01-1 1h-2v6l-3 2v-8H8v8l-3-2V7H3a1 1 0 01-1-1V4a1 1 0 011-1h2V5z"/></svg>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">{{ $announcement->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $announcement->body }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-[8px]">{{ strtoupper(substr($announcement->user->name, 0, 1)) }}</div>
                                    {{ $announcement->user->name }}
                                </span>
                                <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                @if($announcement->course)<span class="text-maroon-500 font-bold">{{ $announcement->course->title }}</span>@endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <a href="{{ route('announcements.show', $announcement) }}" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">Read →</a>
                        @if(auth()->user()->id === $announcement->user_id || auth()->user()->isSuperAdmin())
                        <a href="{{ route('announcements.edit', $announcement) }}" class="text-xs text-gray-400 hover:text-gray-600">Edit</a>
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" data-confirm="Delete announcement?" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-danger-500 hover:text-danger-700">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
                <p class="text-gray-400 font-semibold">No announcements yet</p>
            </div>
        @endforelse
    </div>
    <div class="flex justify-center">{{ $announcements->links() }}</div>
</div>
@endsection
