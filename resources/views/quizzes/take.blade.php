@extends('layouts.dashboard')

@section('page_title', $quiz->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-down">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h2>
            @if($quiz->time_limit_minutes)
            <div id="quizTimer" class="px-4 py-2 bg-warning-50 text-warning-700 rounded-xl font-bold text-sm" data-minutes="{{ $quiz->time_limit_minutes }}">Time: {{ $quiz->time_limit_minutes }}:00</div>
            @endif
        </div>
        <form data-ajax data-no-reload="true" id="quizForm" action="{{ route('courses.quizzes.submit', [$course, $quiz, $attempt]) }}" method="POST">
            @csrf
            <div class="space-y-6">
                @foreach($quiz->questions as $idx => $question)
                    <div class="border border-gray-100 rounded-xl p-4">
                        <p class="font-semibold text-gray-800 mb-3"><span class="text-maroon-500">Q{{ $idx + 1 }}.</span> {!! nl2br(e($question->question_text)) !!} <span class="text-xs text-gray-400">({{ $question->points }} pts)</span></p>
                        @if($question->type === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($question->options as $opt)
                                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $opt->id }}" class="w-4 h-4 rounded text-maroon-600">
                                        <span class="text-sm text-gray-600">{{ $opt->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'true_false')
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2"><input type="radio" name="answers[{{ $question->id }}]" value="{{ $question->options->firstWhere('option_text', 'True')?->id ?? 0 }}" class="w-4 h-4 text-maroon-600"> True</label>
                                <label class="flex items-center gap-2"><input type="radio" name="answers[{{ $question->id }}]" value="{{ $question->options->firstWhere('option_text', 'False')?->id ?? 0 }}" class="w-4 h-4 text-maroon-600"> False</label>
                            </div>
                        @else
                            <textarea name="answers[{{ $question->id }}]" rows="3" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none" placeholder="Your answer..."></textarea>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold hover:scale-105 transition-all">Submit Quiz</button>
            </div>
        </form>
    </div>
</div>
@if($quiz->time_limit_minutes)
<script>
(function() {
    const timerEl = document.getElementById('quizTimer');
    const minutes = parseInt(timerEl.dataset.minutes);
    let totalSeconds = minutes * 60;
    const interval = setInterval(() => {
        totalSeconds--;
        if (totalSeconds <= 0) {
            clearInterval(interval);
            document.getElementById('quizForm').submit();
        }
        const m = Math.floor(totalSeconds / 60);
        const s = totalSeconds % 60;
        timerEl.textContent = `Time: ${m}:${s.toString().padStart(2, '0')}`;
        if (totalSeconds < 60) timerEl.classList.replace('bg-warning-50', 'bg-danger-50').replace('text-warning-700', 'text-danger-700');
    }, 1000);
})();
</script>
@endif
@endsection
