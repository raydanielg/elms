@extends('layouts.dashboard')

@section('page_title', 'Assignments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Assignments</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
        </div>
        @if(auth()->user()->id === $course->owner_id || auth()->user()->isSuperAdmin())
        <a href="{{ route('courses.assignments.create', $course) }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Assignment</a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 stagger">
        @forelse($assignments as $assignment)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-info-400 to-info-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    </div>
                    @if($assignment->due_date)
                    <span class="text-xs {{ now()->isAfter($assignment->due_date) ? 'text-danger-500' : 'text-warning-600' }} font-bold">Due: {{ $assignment->due_date->format('M d, Y') }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-800">{{ $assignment->title }}</h3>
                <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $assignment->instructions }}</p>
                <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                    <span>{{ $assignment->submissions_count }} submissions</span>
                    <span>Max: {{ $assignment->max_points }} pts</span>
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100 transition-all">View</a>
                    @if(auth()->user()->id === $course->owner_id)
                    <a href="{{ route('courses.assignments.edit', [$course, $assignment]) }}" class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold">Edit</a>
                    <form action="{{ route('courses.assignments.destroy', [$course, $assignment]) }}" method="POST" data-confirm="Delete assignment?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <p class="text-gray-400 font-semibold">No assignments yet</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
