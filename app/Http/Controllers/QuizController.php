<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $quizzes = $course->quizzes()->withCount('questions')->get();
        return view('quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        return view('quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_answers_after' => 'boolean',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $validated['course_id'] = $course->id;
        $quiz = Quiz::create($validated);

        return response()->json(['message' => 'Quiz created successfully!', 'redirect' => route('courses.quizzes.show', [$course, $quiz])]);
    }

    public function show(Course $course, Quiz $quiz)
    {
        $quiz->load('questions.options');
        $attempts = $quiz->attempts()->where('user_id', auth()->id())->latest()->get();
        return view('quizzes.show', compact('course', 'quiz', 'attempts'));
    }

    public function edit(Course $course, Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('quizzes.edit', compact('course', 'quiz'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_answers_after' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $quiz->update($validated);
        return response()->json(['message' => 'Quiz updated successfully!']);
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted successfully!']);
    }

    public function addQuestion(Request $request, Course $course, Quiz $quiz)
    {
        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,fill_blank,short_answer,essay',
            'question_text' => 'required|string',
            'points' => 'nullable|integer|min:1',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);

        $validated['quiz_id'] = $quiz->id;
        $validated['sort_order'] = $quiz->questions()->count();

        $options = $validated['options'] ?? [];
        unset($validated['options']);

        $question = Question::create($validated);

        foreach ($options as $idx => $opt) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct' => $opt['is_correct'] ?? false,
                'sort_order' => $idx,
            ]);
        }

        return response()->json(['message' => 'Question added successfully!']);
    }

    public function destroyQuestion(Course $course, Quiz $quiz, Question $question)
    {
        $question->delete();
        return response()->json(['message' => 'Question deleted successfully!']);
    }

    public function start(Course $course, Quiz $quiz)
    {
        $attempts = $quiz->attempts()->where('user_id', auth()->id())->count();
        if ($attempts >= $quiz->max_attempts) {
            return response()->json(['message' => 'You have reached the maximum number of attempts.'], 422);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'max_score' => $quiz->questions->sum('points'),
            'started_at' => now(),
        ]);

        $quiz->load('questions.options');
        return view('quizzes.take', compact('course', 'quiz', 'attempt'));
    }

    public function submit(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $answers = $request->input('answers', []);
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;
            $answerText = null;
            $selectedOptions = null;

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $selectedOptions = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                $correctIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = !empty(array_diff($correctIds, $selectedOptions)) === false && !empty($selectedOptions);
                if ($isCorrect) $earnedPoints += $question->points;
            } elseif ($question->type === 'fill_blank' || $question->type === 'short_answer') {
                $answerText = $userAnswer;
                if ($question->correct_answer && strcasecmp(trim($userAnswer), trim($question->correct_answer)) === 0) {
                    $isCorrect = true;
                    $earnedPoints += $question->points;
                }
            } else {
                $answerText = $userAnswer;
            }

            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'answer_text' => $answerText,
                'selected_options' => $selectedOptions,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]);
        }

        $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $attempt->update([
            'score' => $earnedPoints,
            'percentage' => $percentage,
            'passed' => $percentage >= $quiz->pass_score,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => $percentage >= $quiz->pass_score ? 'Congratulations! You passed the quiz!' : 'Quiz submitted. You did not pass this time.',
            'redirect' => route('courses.quizzes.result', [$course, $quiz, $attempt])
        ]);
    }

    public function result(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $attempt->load('answers.question');
        return view('quizzes.result', compact('course', 'quiz', 'attempt'));
    }
}
