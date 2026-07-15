@extends('layouts.dashboard')

@section('page_title', $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-maroon-500 font-bold hover:text-maroon-700">← Back to {{ $course->title }}</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        {{-- Lesson Content --}}
        <div class="bg-gray-900 aspect-video flex items-center justify-center">
            @if($lesson->content_type === 'video' && $lesson->video_url)
                <video controls class="w-full h-full">
                    <source src="{{ $lesson->video_url }}">
                </video>
            @elseif($lesson->content_type === 'iframe' && $lesson->external_link)
                <iframe src="{{ $lesson->external_link }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            @else
                <div class="text-center text-gray-400">
                    <svg class="w-20 h-20 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    <p class="text-sm">{{ ucfirst($lesson->content_type) }} content</p>
                </div>
            @endif
        </div>

        <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold px-2 py-1 rounded-full bg-maroon-50 text-maroon-700">{{ ucfirst($lesson->content_type) }}</span>
                <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} minutes</span>
            </div>
            <h2 class="text-xl font-bold text-gray-800">{{ $lesson->title }}</h2>
            @if($lesson->description)
                <p class="text-sm text-gray-500 mt-2">{{ $lesson->description }}</p>
            @endif

            @if($lesson->content_type === 'text' && $lesson->text_content)
                <div class="prose prose-sm max-w-none mt-4 text-gray-600">{!! nl2br(e($lesson->text_content)) !!}</div>
            @endif

            @if($lesson->content_type === 'link' && $lesson->external_link)
                <a href="{{ $lesson->external_link }}" target="_blank" class="inline-block mt-4 px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700 transition-all">Open External Link →</a>
            @endif

            @if($enrolled)
            <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between">
                <button data-action-url="{{ route('courses.lessons.complete', [$course, $lesson]) }}" data-action-method="POST" class="px-5 py-2.5 bg-gradient-to-r from-success-400 to-success-600 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">
                    Mark as Complete ✓
                </button>
                <div class="text-sm text-gray-400">
                    Progress: {{ round($enrolled->progress) }}%
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Quizzes --}}
    @if($lesson->quizzes->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-3">Quizzes for this Lesson</h3>
        <div class="space-y-2">
            @foreach($lesson->quizzes as $quiz)
                <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-400">{{ $quiz->questions->count() }} questions · Pass: {{ $quiz->pass_score }}%</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
