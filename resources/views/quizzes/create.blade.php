@extends('layouts.dashboard')

@section('page_title', 'Create Quiz')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Create Quiz</h2><p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('courses.quizzes.store', $course) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Quiz Title *</label><input type="text" name="title" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Time Limit (min)</label><input type="number" name="time_limit_minutes" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Pass Score (%) *</label><input type="number" name="pass_score" required min="0" max="100" value="50" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Max Attempts *</label><input type="number" name="max_attempts" required min="1" value="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="shuffle_questions" class="w-4 h-4 rounded text-maroon-600"> Shuffle Questions</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="shuffle_answers" class="w-4 h-4 rounded text-maroon-600"> Shuffle Answers</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="show_answers_after" checked class="w-4 h-4 rounded text-maroon-600"> Show Answers After</label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Create Quiz</button>
                <a href="{{ route('courses.quizzes.index', $course) }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
