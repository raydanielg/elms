@extends('layouts.dashboard')

@section('page_title', 'Quizzes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quizzes</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
        </div>
        @if(auth()->user()->id === $course->owner_id || auth()->user()->isSuperAdmin())
        <a href="{{ route('courses.quizzes.create', $course) }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Quiz</a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 stagger">
        @forelse($quizzes as $quiz)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                </div>
                <h3 class="font-bold text-gray-800">{{ $quiz->title }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ $quiz->questions_count }} questions · Pass: {{ $quiz->pass_score }}% · Max attempts: {{ $quiz->max_attempts }}</p>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100 transition-all">View</a>
                    @if(auth()->user()->id === $course->owner_id)
                    <a href="{{ route('courses.quizzes.edit', [$course, $quiz]) }}" class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-100">Edit</a>
                    <form action="{{ route('courses.quizzes.destroy', [$course, $quiz]) }}" method="POST" data-confirm="Delete quiz?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold hover:bg-danger-100">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <p class="text-gray-400 font-semibold">No quizzes yet</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
