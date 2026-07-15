@extends('layouts.dashboard')

@section('page_title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Notifications</h2><p class="text-sm text-gray-500 mt-1">Stay updated with your latest activities</p></div>
        <button data-action-url="{{ route('notifications.readAll') }}" data-action-method="POST" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Mark All Read</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <div class="flex items-start gap-3 p-4 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-maroon-50/30' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $notification->type === 'success' ? 'bg-success-100 text-success-600' : ($notification->type === 'error' ? 'bg-danger-100 text-danger-600' : ($notification->type === 'warning' ? 'bg-warning-100 text-warning-600' : 'bg-info-100 text-info-600')) }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">{{ $notification->title }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $notification->body }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                    <button data-action-url="{{ route('notifications.read', $notification) }}" data-action-method="POST" data-no-reload="true" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">Mark Read</button>
                    @endif
                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" data-confirm="Delete notification?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-danger-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg></button>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659"/></svg>
                    <p class="text-gray-400 font-semibold">No notifications</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="flex justify-center">{{ $notifications->links() }}</div>
</div>
@endsection
