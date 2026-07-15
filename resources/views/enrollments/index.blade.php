@extends('layouts.dashboard')

@section('page_title', 'My Enrollments')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Enrollments</h2><p class="text-sm text-gray-500 mt-1">Track your course progress</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($enrollments as $enrollment)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-sm">
                <div class="h-32 bg-gradient-to-br from-maroon-500 to-maroon-700 relative">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13"/></svg>
                    </div>
                    <div class="absolute bottom-3 right-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $enrollment->status === 'completed' ? 'bg-success-500 text-white' : 'bg-white/20 text-white' }}">{{ ucfirst($enrollment->status) }}</span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-800 truncate">{{ $enrollment->course->title }}</h3>
                    <p class="text-xs text-gray-400 mt-1">By {{ $enrollment->course->owner->name }}</p>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Progress</span><span class="font-bold text-gray-700">{{ round($enrollment->progress) }}%</span></div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-maroon-500 to-orange-400 rounded-full transition-all duration-700" style="width: {{ $enrollment->progress }}%"></div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('courses.show', $enrollment->course) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100 transition-all">Continue</a>
                        <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST" data-confirm="Unenroll from this course?" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold">Unenroll</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                <p class="text-gray-400 font-semibold">No enrollments yet</p>
                <a href="{{ route('marketplace.index') }}" class="inline-block mt-3 px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700">Browse Courses</a>
            </div>
        @endforelse
    </div>
    <div class="flex justify-center">{{ $enrollments->links() }}</div>
</div>
@endsection
