@extends('layouts.dashboard')

@section('page_title', $course->title)

@section('content')
<div class="space-y-6">
    {{-- Course Header --}}
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $course->status === 'published' ? 'bg-success-500' : 'bg-gray-500' }}">{{ ucfirst($course->status) }}</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ ucfirst($course->level) }}</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ ucfirst($course->visibility) }}</span>
            </div>
            <h2 class="text-2xl font-bold">{{ $course->title }}</h2>
            <p class="text-maroon-100 text-sm mt-1 max-w-2xl">{{ $course->description ?? 'No description' }}</p>
            <div class="flex items-center gap-4 mt-4 text-sm">
                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87"/></svg> {{ $course->enrollment_count }} students</span>
                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13"/></svg> {{ $course->lessons_count }} lessons</span>
                <span class="font-bold">{{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}</span>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="flex flex-wrap gap-3 animate-slide-up">
        @if(!$enrolled && $course->status === 'published')
            <button data-action-url="{{ route('courses.enroll', $course) }}" data-action-method="POST" class="px-5 py-2.5 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">
                Enroll Now
            </button>
        @elseif($enrolled)
            <a href="{{ route('courses.lessons.show', [$course, $course->modules->first()?->lessons->first()?->id ?? 1]) }}" class="px-5 py-2.5 bg-gradient-to-r from-success-400 to-success-600 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">
                Continue Learning
            </a>
        @endif
        @if(auth()->user()->id === $course->owner_id || auth()->user()->isSuperAdmin())
            <a href="{{ route('courses.edit', $course) }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all">Edit Course</a>
            <a href="{{ route('courses.quizzes.index', $course) }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all">Quizzes</a>
            <a href="{{ route('courses.assignments.index', $course) }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all">Assignments</a>
            <form action="{{ route('courses.destroy', $course) }}" method="POST" data-confirm="Delete this course?" data-confirm-text="Yes, delete it!" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-danger-50 text-danger-600 rounded-xl font-bold text-sm hover:bg-danger-100 transition-all">Delete</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Course Content --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Course Content</h3>
                    @if(auth()->user()->id === $course->owner_id)
                    <button onclick="openModal('moduleModal')" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">+ Add Module</button>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($course->modules as $module)
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $module->title }}</h4>
                                @if(auth()->user()->id === $course->owner_id)
                                <div class="flex gap-1">
                                    <button onclick="openModal('lessonModal{{ $module->id }}')" class="text-xs text-maroon-500 font-bold hover:text-maroon-700">+ Lesson</button>
                                    <form action="{{ route('courses.modules.destroy', [$course, $module]) }}" method="POST" data-confirm="Delete module?" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-danger-500 font-bold hover:text-danger-700">Delete</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            <div class="space-y-1 ml-2">
                                @foreach($module->lessons as $lesson)
                                    <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="flex-1">{{ $lesson->title }}</span>
                                        @if($lesson->is_preview)<span class="text-[10px] font-bold text-success-600 bg-success-50 px-1.5 py-0.5 rounded">Preview</span>@endif
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Lesson Modal --}}
                            @if(auth()->user()->id === $course->owner_id)
                            <div id="lessonModal{{ $module->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-lg animate-scale-in">
                                    <h3 class="font-bold text-gray-800 mb-4">Add Lesson to {{ $module->title }}</h3>
                                    <form data-ajax data-close-modal="lessonModal{{ $module->id }}" data-reset-on-success="true" action="{{ route('lessons.store', $module) }}" method="POST">
                                        @csrf
                                        <div class="space-y-4">
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Title *</label><input type="text" name="title" required class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Content Type</label><select name="content_type" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"><option value="video">Video</option><option value="text">Text</option><option value="pdf">PDF</option><option value="link">External Link</option></select></div>
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Video URL</label><input type="text" name="video_url" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></div>
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Text Content</label><textarea name="text_content" rows="3" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></textarea></div>
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Duration (min)</label><input type="number" name="duration_minutes" min="0" value="10" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></div>
                                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_preview" class="w-4 h-4 rounded text-maroon-600"> Free Preview</label>
                                        </div>
                                        <div class="flex gap-3 mt-5">
                                            <button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add Lesson</button>
                                            <button type="button" onclick="closeModal('lessonModal{{ $module->id }}')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-sm">No modules yet. Add your first module to start building the course.</div>
                    @endforelse
                </div>
            </div>

            {{-- Reviews --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-4">Reviews ({{ $course->reviews->count() }})</h3>
                <div class="space-y-4">
                    @forelse($course->reviews as $review)
                        <div class="flex gap-3 pb-4 border-b border-gray-50 last:border-0">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $review->user->name }}</span>
                                    <span class="text-xs text-warning-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No reviews yet</p>
                    @endforelse
                </div>
                @if($enrolled && auth()->user()->reviews()->where('course_id', $course->id)->count() === 0)
                <form data-ajax data-reset-on-success="true" action="{{ route('reviews.store', $course) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                    @csrf
                    <div class="flex gap-3">
                        <select name="rating" class="px-3 py-2 rounded-xl border border-gray-200 text-sm"><option value="5">★★★★★</option><option value="4">★★★★☆</option><option value="3">★★★☆☆</option><option value="2">★★☆☆☆</option><option value="1">★☆☆☆☆</option></select>
                        <input type="text" name="comment" placeholder="Write a review..." class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                        <button type="submit" class="px-4 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Post</button>
                    </div>
                </form>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">Course Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Instructor</span><span class="font-semibold text-gray-700">{{ $course->owner->name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Category</span><span class="font-semibold text-gray-700">{{ $course->category?->name ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Language</span><span class="font-semibold text-gray-700">{{ $course->language }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Duration</span><span class="font-semibold text-gray-700">{{ $course->duration_hours }}h</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Certificate</span><span class="font-semibold {{ $course->has_certificate ? 'text-success-600' : 'text-gray-400' }}">{{ $course->has_certificate ? 'Yes' : 'No' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Rating</span><span class="font-semibold text-warning-500">{{ $course->average_rating }} ★</span></div>
                </div>
            </div>
            @if($enrolled && $enrolled->status === 'completed' && $course->has_certificate)
            <div class="bg-gradient-to-br from-orange-50 to-maroon-50 rounded-2xl p-5 border border-orange-100 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-2">Course Completed! 🎉</h3>
                <p class="text-sm text-gray-500 mb-3">Generate your certificate of completion.</p>
                <button data-action-url="{{ route('certificates.generate', $course) }}" data-action-method="POST" class="w-full px-4 py-2.5 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Get Certificate</button>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Module Modal --}}
@if(auth()->user()->id === $course->owner_id)
<div id="moduleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
        <h3 class="font-bold text-gray-800 mb-4">Add Module</h3>
        <form data-ajax data-close-modal="moduleModal" data-reset-on-success="true" action="{{ route('courses.modules.store', $course) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Module Title *</label><input type="text" name="title" required class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></textarea></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add Module</button>
                <button type="button" onclick="closeModal('moduleModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
