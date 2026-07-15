@extends('layouts.dashboard')

@section('page_title', 'Webhook Endpoints')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Webhook Endpoints</h2><p class="text-sm text-gray-500 mt-1">Outgoing webhooks to external systems</p></div>
        <div class="flex gap-2">
            <a href="{{ route('webhooks.logs') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Logs</a>
            <button onclick="openModal('addWebhookModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Add Endpoint</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">URL</th><th class="px-5 py-3 text-left font-bold">Events</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($endpoints as $endpoint)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-700 truncate max-w-xs">{{ $endpoint->url }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $endpoint->events ? implode(', ', $endpoint->events) : 'All events' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $endpoint->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $endpoint->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-5 py-3">
                            <form action="{{ route('webhooks.destroy', $endpoint) }}" method="POST" data-confirm="Remove this endpoint?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No webhook endpoints</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addWebhookModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Add Webhook Endpoint</h3>
            <form data-ajax data-close-modal="addWebhookModal" action="{{ route('webhooks.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">URL *</label><input type="url" name="url" required placeholder="https://your-app.com/webhooks/elms" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Events (comma-separated)</label><input type="text" name="events" placeholder="student.enrolled, course.completed" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"><p class="text-xs text-gray-400 mt-1">Leave empty for all events</p></div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-maroon-600"> Active</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add</button><button type="button" onclick="closeModal('addWebhookModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
