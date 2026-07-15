@extends('layouts.dashboard')

@section('page_title', 'Currencies')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Currencies</h2><p class="text-sm text-gray-500 mt-1">Manage supported currencies and exchange rates</p></div>
        <button onclick="openModal('addCurrencyModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Add Currency</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Code</th><th class="px-5 py-3 text-left font-bold">Name</th><th class="px-5 py-3 text-left font-bold">Symbol</th><th class="px-5 py-3 text-left font-bold">Exchange Rate</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($currencies as $currency)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-bold text-gray-800">{{ $currency->code }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $currency->name }}</td>
                        <td class="px-5 py-3 font-semibold">{{ $currency->symbol }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $currency->exchange_rate }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $currency->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $currency->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-5 py-3">
                            <form action="{{ route('currencies.destroy', $currency) }}" method="POST" data-confirm="Remove this currency?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No currencies configured</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addCurrencyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Add Currency</h3>
            <form data-ajax data-close-modal="addCurrencyModal" action="{{ route('currencies.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Code (3 letters) *</label><input type="text" name="code" required maxlength="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm uppercase"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Symbol *</label><input type="text" name="symbol" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Exchange Rate (vs USD) *</label><input type="number" name="exchange_rate" required step="0.000001" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-maroon-600"> Active</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add</button><button type="button" onclick="closeModal('addCurrencyModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
