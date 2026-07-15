@extends('layouts.dashboard')

@section('page_title', 'Courses')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Courses</h2>
            <p class="text-sm text-gray-500 mt-1">Manage and explore all your courses</p>
        </div>
        @if(auth()->user()->hasRole(['admin', 'teacher', 'solo_teacher']))
        <a href="{{ route('courses.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Course
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-slide-up">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..." class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 focus:ring-2 focus:ring-maroon-100 outline-none transition-all">
            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <select name="category" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700 transition-all">Filter</button>
        </form>
    </div>

    {{-- Course Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($courses as $course)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-sm group">
                <div class="relative h-40 bg-gradient-to-br {{ $course->level === 'beginner' ? 'from-info-400 to-info-600' : ($course->level === 'intermediate' ? 'from-orange-400 to-orange-600' : 'from-maroon-500 to-maroon-700') }} overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $course->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $course->status === 'published' ? 'bg-success-500 text-white' : ($course->status === 'draft' ? 'bg-gray-500 text-white' : 'bg-warning-500 text-white') }}">{{ ucfirst($course->status) }}</span>
                    </div>
                    <div class="absolute top-3 right-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-black/40 text-white backdrop-blur-sm">{{ ucfirst($course->level) }}</span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-800 truncate group-hover:text-maroon-600 transition-colors">{{ $course->title }}</h3>
                    <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $course->description ?? 'No description' }}</p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87"/></svg>
                            {{ $course->enrollment_count }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13"/></svg>
                            {{ $course->lessons_count }}
                        </span>
                        @if($course->price > 0)
                            <span class="font-bold text-orange-600">${{ number_format($course->price, 2) }}</span>
                        @else
                            <span class="font-bold text-success-600">Free</span>
                        @endif
                    </div>
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('courses.show', $course) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100 transition-all">View</a>
                        @if(auth()->user()->id === $course->owner_id || auth()->user()->isSuperAdmin())
                            <a href="{{ route('courses.edit', $course) }}" class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-100 transition-all">Edit</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
                <p class="text-gray-400 font-semibold">No courses found</p>
                <p class="text-gray-300 text-sm mt-1">Create your first course to get started</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $courses->withQueryString()->links() }}
    </div>
</div>
@endsection
