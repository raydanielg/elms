@extends('layouts.dashboard')

@section('page_title', $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <a href="{{ route('courses.quizzes.index', $course) }}" class="text-sm text-maroon-500 font-bold hover:text-maroon-700">← Back to Quizzes</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $quiz->description }}</p>
                <div class="flex gap-4 mt-3 text-xs text-gray-400">
                    <span>{{ $quiz->questions->count() }} questions</span>
                    <span>Pass: {{ $quiz->pass_score }}%</span>
                    <span>Max attempts: {{ $quiz->max_attempts }}</span>
                    @if($quiz->time_limit_minutes)<span>Time: {{ $quiz->time_limit_minutes }} min</span>@endif
                </div>
            </div>
            @if(auth()->user()->id === $course->owner_id)
            <button onclick="openModal('questionModal')" class="px-4 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700">+ Add Question</button>
            @endif
        </div>
    </div>

    {{-- Questions --}}
    <div class="space-y-3">
        @forelse($quiz->questions as $idx => $question)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <span class="text-xs font-bold text-maroon-500">Q{{ $idx + 1 }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ ucfirst(str_replace('_', ' ', $question->type)) }} · {{ $question->points }} pts</span>
                        <p class="font-semibold text-gray-800 mt-2">{!! nl2br(e($question->question_text)) !!}</p>
                        @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                            <div class="mt-3 space-y-1">
                                @foreach($question->options as $opt)
                                    <div class="flex items-center gap-2 text-sm {{ $opt->is_correct ? 'text-success-700 font-semibold' : 'text-gray-500' }}">
                                        <span class="w-5 h-5 rounded-full {{ $opt->is_correct ? 'bg-success-500 text-white' : 'bg-gray-200' }} flex items-center justify-center text-xs">{{ $opt->is_correct ? '✓' : '' }}</span>
                                        {{ $opt->option_text }}
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->correct_answer)
                            <p class="text-sm text-success-700 mt-2"><strong>Answer:</strong> {{ $question->correct_answer }}</p>
                        @endif
                    </div>
                    @if(auth()->user()->id === $course->owner_id)
                    <form action="{{ route('courses.quizzes.questions.destroy', [$course, $quiz, $question]) }}" method="POST" data-confirm="Delete question?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-danger-500 text-xs font-bold hover:text-danger-700">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
                <p class="text-gray-400 font-semibold">No questions yet</p>
                <p class="text-gray-300 text-sm mt-1">Add questions to this quiz</p>
            </div>
        @endforelse
    </div>

    {{-- Start Quiz --}}
    @if(auth()->user()->isStudent() && $quiz->is_published)
    <div class="bg-gradient-to-br from-maroon-50 to-orange-50 rounded-2xl p-6 border border-maroon-100 text-center animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-2">Ready to take the quiz?</h3>
        <p class="text-sm text-gray-500 mb-4">Attempts used: {{ $attempts->count() }} / {{ $quiz->max_attempts }}</p>
        @if($attempts->count() < $quiz->max_attempts)
        <a href="{{ route('courses.quizzes.start', [$course, $quiz]) }}" class="px-6 py-3 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold hover:scale-105 transition-all inline-block">Start Quiz →</a>
        @else
        <p class="text-sm text-danger-600 font-bold">You have used all your attempts.</p>
        @endif
        @if($attempts->count() > 0)
        <div class="mt-4 pt-4 border-t border-maroon-100">
            <p class="text-xs font-bold text-gray-500 mb-2">Previous Attempts:</p>
            @foreach($attempts as $attempt)
                <a href="{{ route('courses.quizzes.result', [$course, $quiz, $attempt]) }}" class="inline-block mx-1 px-3 py-1 rounded-lg text-xs font-bold {{ $attempt->passed ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">
                    {{ $attempt->percentage }}% {{ $attempt->passed ? '✓' : '✕' }}
                </a>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</div>

{{-- Question Modal --}}
@if(auth()->user()->id === $course->owner_id)
<div id="questionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto">
    <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-2xl animate-scale-in my-8">
        <h3 class="font-bold text-gray-800 mb-4">Add Question</h3>
        <form data-ajax data-close-modal="questionModal" data-reset-on-success="true" action="{{ route('courses.quizzes.questions.store', [$course, $quiz]) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Question Type *</label>
                    <select name="type" id="qType" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm" onchange="toggleOptions()">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True / False</option>
                        <option value="fill_blank">Fill in the Blank</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Question Text *</label><textarea name="question_text" required rows="3" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1">Points</label><input type="number" name="points" min="1" value="1" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></div>
                    <div id="correctAnswerDiv"><label class="block text-sm font-bold text-gray-700 mb-1">Correct Answer (for text types)</label><input type="text" name="correct_answer" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div id="optionsContainer">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Options (check correct ones)</label>
                    <div id="optionsList" class="space-y-2"></div>
                    <button type="button" onclick="addOption()" class="text-xs font-bold text-maroon-500 hover:text-maroon-700 mt-2">+ Add Option</button>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add Question</button>
                <button type="button" onclick="closeModal('questionModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
let optIdx = 0;
function addOption() {
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2';
    div.innerHTML = `<input type="checkbox" name="options[${optIdx}][is_correct]" class="w-4 h-4 rounded text-maroon-600"><input type="text" name="options[${optIdx}][text]" placeholder="Option text" required class="flex-1 px-3 py-1.5 rounded-lg border border-gray-200 text-sm"><button type="button" onclick="this.parentElement.remove()" class="text-danger-500 text-xs">✕</button>`;
    document.getElementById('optionsList').appendChild(div);
    optIdx++;
}
function toggleOptions() {
    const type = document.getElementById('qType').value;
    const showOpts = ['multiple_choice'].includes(type);
    const showAns = ['fill_blank', 'short_answer'].includes(type);
    document.getElementById('optionsContainer').style.display = showOpts ? '' : 'none';
    document.getElementById('correctAnswerDiv').style.display = showAns ? '' : 'none';
    if (type === 'true_false') {
        document.getElementById('optionsList').innerHTML = '';
        optIdx = 0;
        addOption(); document.querySelector('#optionsList input[type=text]').value = 'True';
        addOption(); document.querySelector('#optionsList:last-child input[type=text]')?.value = 'False';
    }
}
toggleOptions(); addOption(); addOption();
</script>
@endif
@endsection
