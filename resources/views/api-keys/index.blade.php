@extends('layouts.dashboard')

@section('page_title', 'API Keys')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">API Keys</h2><p class="text-sm text-gray-500 mt-1">Developer API access keys</p></div>
        <button onclick="openModal('addKeyModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Generate Key</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Name</th><th class="px-5 py-3 text-left font-bold">Key</th><th class="px-5 py-3 text-left font-bold">Last Used</th><th class="px-5 py-3 text-left font-bold">Expires</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($keys as $key)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $key->name }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-400">{{ $key->key_prefix }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $key->last_used_at?->format('M d, Y') ?? 'Never' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $key->expires_at?->format('M d, Y') ?? 'Never' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('api-keys.destroy', $key) }}" method="POST" data-confirm="Revoke this API key?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Revoke</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No API keys generated</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addKeyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Generate API Key</h3>
            <form data-ajax data-close-modal="addKeyModal" action="{{ route('api-keys.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required placeholder="e.g. Mobile App Integration" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Expires At</label><input type="date" name="expires_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Generate</button><button type="button" onclick="closeModal('addKeyModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
