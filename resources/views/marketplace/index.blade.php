@extends('layouts.dashboard')

@section('page_title', 'Marketplace')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down">
        <h2 class="text-2xl font-bold text-gray-800">Course Marketplace</h2>
        <p class="text-sm text-gray-500 mt-1">Discover and enroll in courses from independent instructors</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-slide-up">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..." class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
            <select name="category" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach
            </select>
            <select name="level" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">All Levels</option>
                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
            <select name="price" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">All Prices</option>
                <option value="free" {{ request('price') === 'free' ? 'selected' : '' }}>Free</option>
                <option value="paid" {{ request('price') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <select name="sort" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">Latest</option>
                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top Rated</option>
            </select>
            <button type="submit" class="px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm">Search</button>
        </form>
    </div>

    {{-- Course Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($courses as $course)
            <a href="{{ route('marketplace.show', $course) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-sm group">
                <div class="relative h-40 bg-gradient-to-br {{ $course->level === 'beginner' ? 'from-info-400 to-info-600' : ($course->level === 'intermediate' ? 'from-orange-400 to-orange-600' : 'from-maroon-500 to-maroon-700') }} overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $course->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13"/></svg>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3">
                        @if($course->price > 0)
                            <span class="text-sm font-bold px-3 py-1 rounded-full bg-white/90 text-orange-600">${{ number_format($course->price, 2) }}</span>
                        @else
                            <span class="text-sm font-bold px-3 py-1 rounded-full bg-success-500 text-white">FREE</span>
                        @endif
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-800 truncate group-hover:text-maroon-600 transition-colors">{{ $course->title }}</h3>
                    <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $course->description ?? 'No description' }}</p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                        <span>{{ $course->owner->name }}</span>
                        <span class="text-warning-500">{{ $course->average_rating }} ★</span>
                        <span>{{ $course->enrollment_count }} students</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                <p class="text-gray-400 font-semibold">No courses found</p>
                <p class="text-gray-300 text-sm mt-1">Try adjusting your filters</p>
            </div>
        @endforelse
    </div>

    <div class="flex justify-center">{{ $courses->withQueryString()->links() }}</div>
</div>
@endsection
