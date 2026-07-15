@extends('layouts.dashboard')

@section('page_title', $course->title)

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 rounded-2xl p-6 text-white animate-slide-down relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-400/10 rounded-full -mr-32 -mt-32"></div>
        <div class="relative z-10">
            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/10">{{ ucfirst($course->level) }}</span>
            <h2 class="text-2xl font-bold mt-2">{{ $course->title }}</h2>
            <p class="text-maroon-100 text-sm mt-1 max-w-2xl">{{ $course->description }}</p>
            <div class="flex items-center gap-4 mt-4 text-sm">
                <span>By {{ $course->owner->name }}</span>
                <span class="text-warning-400">{{ $course->average_rating }} ★</span>
                <span>{{ $course->enrollment_count }} students</span>
                <span class="font-bold text-lg">{{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'FREE' }}</span>
            </div>
        </div>
    </div>

    <div class="flex gap-3 animate-slide-up">
        @if(auth()->check() && !$enrolled)
            <button data-action-url="{{ route('courses.enroll', $course) }}" data-action-method="POST" class="px-6 py-3 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-xl font-bold hover:scale-105 transition-all">
                {{ $course->price > 0 ? 'Buy & Enroll' : 'Enroll Free' }}
            </button>
        @elseif($enrolled)
            <a href="{{ route('courses.show', $course) }}" class="px-6 py-3 bg-gradient-to-r from-success-400 to-success-600 text-white rounded-xl font-bold hover:scale-105 transition-all">Go to Course →</a>
        @else
            <a href="{{ route('login') }}" class="px-6 py-3 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-xl font-bold hover:scale-105 transition-all">Login to Enroll</a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
            <h3 class="font-bold text-gray-800 mb-4">Course Content</h3>
            <div class="space-y-3">
                @foreach($course->modules as $module)
                    <div>
                        <h4 class="font-semibold text-gray-700 text-sm py-2">{{ $module->title }}</h4>
                        <div class="space-y-1 ml-2">
                            @foreach($module->lessons as $lesson)
                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                    <span class="flex-1">{{ $lesson->title }}</span>
                                    @if($lesson->is_preview)<span class="text-[10px] font-bold text-success-600 bg-success-50 px-1.5 py-0.5 rounded">Preview</span>@endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">About Instructor</h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-maroon-400 to-maroon-600 flex items-center justify-center text-white font-bold">{{ strtoupper(substr($course->owner->name, 0, 1)) }}</div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $course->owner->name }}</p>
                        <p class="text-xs text-gray-400">{{ $course->owner->bio ?? 'Instructor' }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-slide-up">
                <h3 class="font-bold text-gray-800 mb-3">Reviews</h3>
                <div class="space-y-3">
                    @forelse($course->reviews as $review)
                        <div class="pb-3 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800 text-sm">{{ $review->user->name }}</span>
                                <span class="text-xs text-warning-500">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No reviews yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
