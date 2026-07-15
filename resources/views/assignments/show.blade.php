@extends('layouts.dashboard')

@section('page_title', $assignment->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <a href="{{ route('courses.assignments.index', $course) }}" class="text-sm text-maroon-500 font-bold hover:text-maroon-700">← Back to Assignments</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $assignment->title }}</h2>
                <p class="text-sm text-gray-500 mt-2">{!! nl2br(e($assignment->instructions)) !!}</p>
                <div class="flex gap-4 mt-3 text-xs text-gray-400">
                    <span>Max: {{ $assignment->max_points }} pts</span>
                    @if($assignment->due_date)<span>Due: {{ $assignment->due_date->format('M d, Y H:i') }}</span>@endif
                    @if($assignment->allow_late_submission)<span>Late allowed ({{ $assignment->late_penalty_percent }}% penalty)</span>@endif
                </div>
            </div>
            @if(auth()->user()->id === $course->owner_id)
            <a href="{{ route('courses.assignments.edit', [$course, $assignment]) }}" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl font-bold text-sm">Edit</a>
            @endif
        </div>
    </div>

    @if(auth()->user()->isStudent())
        @if(!$mySubmission)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Submit Your Work</h3>
            <form data-ajax action="{{ route('courses.assignments.submit', [$course, $assignment]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Written Answer</label><textarea name="submission_text" rows="5" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></textarea></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Upload File (max 10MB)</label><input type="file" name="file" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold"></div>
                </div>
                <button type="submit" class="mt-4 px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Submit Assignment</button>
            </form>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-3">Your Submission</h3>
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $mySubmission->status === 'graded' ? 'bg-success-100 text-success-700' : ($mySubmission->status === 'late' ? 'bg-warning-100 text-warning-700' : 'bg-info-100 text-info-700') }}">{{ ucfirst($mySubmission->status) }}</span>
                @if($mySubmission->status === 'graded')
                <span class="font-bold text-gray-800">{{ $mySubmission->score }}/{{ $assignment->max_points }} pts</span>
                @endif
            </div>
            @if($mySubmission->submission_text)<p class="text-sm text-gray-600">{!! nl2br(e($mySubmission->submission_text)) !!}</p>@endif
            @if($mySubmission->file_path)<a href="{{ Storage::url($mySubmission->file_path) }}" target="_blank" class="text-sm text-maroon-500 font-bold">Download File →</a>@endif
            @if($mySubmission->feedback)<div class="mt-3 p-3 bg-gray-50 rounded-xl"><p class="text-xs font-bold text-gray-500">Feedback:</p><p class="text-sm text-gray-600 mt-1">{{ $mySubmission->feedback }}</p></div>@endif
        </div>
        @endif
    @endif

    @if(auth()->user()->id === $course->owner_id)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Student Submissions ({{ $assignment->submissions->count() }})</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-5 py-3 text-left">Student</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Score</th><th class="px-5 py-3 text-left">Submitted</th><th class="px-5 py-3 text-left">Action</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($assignment->submissions as $sub)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $sub->user->name }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $sub->status === 'graded' ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">{{ ucfirst($sub->status) }}</span></td>
                        <td class="px-5 py-3">{{ $sub->status === 'graded' ? $sub->score . '/' . $assignment->max_points : '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $sub->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3">
                            @if($sub->status !== 'graded')
                            <button onclick="openModal('gradeModal{{ $sub->id }}')" class="text-xs font-bold text-maroon-500">Grade →</button>
                            <div id="gradeModal{{ $sub->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
                                    <h3 class="font-bold text-gray-800 mb-4">Grade: {{ $sub->user->name }}</h3>
                                    <form data-ajax data-close-modal="gradeModal{{ $sub->id }}" action="{{ route('courses.assignments.grade', [$course, $assignment, $sub]) }}" method="POST">
                                        @csrf
                                        <div class="space-y-4">
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Score (max {{ $assignment->max_points }})</label><input type="number" name="score" required min="0" max="{{ $assignment->max_points }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></div>
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Feedback</label><textarea name="feedback" rows="3" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></textarea></div>
                                        </div>
                                        <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Submit Grade</button><button type="button" onclick="closeModal('gradeModal{{ $sub->id }}')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
                                    </form>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">Graded</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No submissions yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
