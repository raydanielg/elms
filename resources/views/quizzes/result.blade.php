@extends('layouts.dashboard')

@section('page_title', 'Quiz Result')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center animate-scale-in">
        <div class="w-20 h-20 rounded-full {{ $attempt->passed ? 'bg-success-100' : 'bg-danger-100' }} flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 {{ $attempt->passed ? 'text-success-600' : 'text-danger-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $attempt->passed ? 'M9 12l2 2 4-4' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
        </div>
        <h2 class="text-2xl font-bold {{ $attempt->passed ? 'text-success-700' : 'text-danger-700' }}">{{ $attempt->passed ? 'Passed!' : 'Not Passed' }}</h2>
        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $attempt->percentage }}%</p>
        <p class="text-sm text-gray-400 mt-1">Score: {{ $attempt->score }} / {{ $attempt->max_score }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">Answer Review</h3>
        <div class="space-y-4">
            @foreach($attempt->answers as $idx => $answer)
                <div class="border border-gray-100 rounded-xl p-4">
                    <div class="flex items-start gap-2">
                        <span class="text-xs font-bold {{ $answer->is_correct ? 'text-success-600' : 'text-danger-600' }}">{{ $answer->is_correct ? '✓' : '✕' }}</span>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800 text-sm">{!! nl2br(e($answer->question->question_text)) !!}</p>
                            <p class="text-sm text-gray-500 mt-1">Your answer: {{ $answer->answer_text ?? (is_array($answer->selected_options) ? implode(', ', $answer->selected_options) : 'N/A') }}</p>
                            @if($answer->feedback)<p class="text-sm text-info-600 mt-1">Feedback: {{ $answer->feedback }}</p>@endif
                        </div>
                        <span class="text-xs text-gray-400">{{ $answer->points_earned }}/{{ $answer->question->points }} pts</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700">Back to Quiz</a>
        @if($attempt->passed === false && $quiz->attempts()->where('user_id', auth()->id())->count() < $quiz->max_attempts)
        <a href="{{ route('courses.quizzes.start', [$course, $quiz]) }}" class="px-5 py-2.5 bg-orange-500 text-white rounded-xl font-bold text-sm hover:bg-orange-600">Try Again</a>
        @endif
    </div>
</div>
@endsection
