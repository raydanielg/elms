@extends('layouts.dashboard')

@section('page_title', 'My Wishlist')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Wishlist</h2><p class="text-sm text-gray-500 mt-1">Courses you want to take later</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($wishlist as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-sm">
            <div class="h-32 bg-gradient-to-br from-maroon-500 to-maroon-700 relative">
                <div class="absolute inset-0 flex items-center justify-center"><svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13"/></svg></div>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-gray-800 truncate">{{ $item->course->title }}</h3>
                <p class="text-xs text-gray-400 mt-1">By {{ $item->course->owner->name }}</p>
                @if($item->course->price > 0)<p class="text-lg font-bold text-maroon-600 mt-2">${{ number_format($item->course->price, 2) }}</p>@else<p class="text-lg font-bold text-success-600 mt-2">Free</p>@endif
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('marketplace.show', $item->course) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100">View</a>
                    <form action="{{ route('wishlist.destroy', $item) }}" method="POST" data-confirm="Remove from wishlist?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <p class="text-gray-400 font-semibold">Your wishlist is empty</p>
            <a href="{{ route('marketplace.index') }}" class="inline-block mt-3 px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Browse Courses</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
