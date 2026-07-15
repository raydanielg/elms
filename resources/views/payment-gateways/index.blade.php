@extends('layouts.dashboard')

@section('page_title', 'Payment Gateways')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Payment Gateways</h2><p class="text-sm text-gray-500 mt-1">Configure payment providers</p></div>
        <button onclick="openModal('addGatewayModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Add Gateway</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($gateways as $gateway)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-maroon-500 to-maroon-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $gateway->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $gateway->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <h3 class="font-bold text-gray-800">{{ $gateway->label }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ ucfirst($gateway->category) }} · Priority {{ $gateway->priority }}</p>
            @if($gateway->supported_currencies)<p class="text-xs text-gray-400 mt-1">Currencies: {{ implode(', ', $gateway->supported_currencies) }}</p>@endif
            <div class="flex gap-2 mt-4">
                <button onclick="editGateway({{ $gateway->id }})" class="text-xs font-bold text-maroon-500">Edit</button>
                <form action="{{ route('payment-gateways.destroy', $gateway) }}" method="POST" data-confirm="Remove this gateway?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-danger-500">Remove</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No payment gateways configured</p></div>
        @endforelse
    </div>

    <a href="{{ route('payment-gateways.logs') }}" class="inline-block px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">View Webhook Logs</a>

    {{-- Add Gateway Modal --}}
    <div id="addGatewayModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Add Payment Gateway</h3>
            <form data-ajax data-close-modal="addGatewayModal" action="{{ route('payment-gateways.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Driver *</label>
                        <select name="driver" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            @foreach($availableDrivers as $driver)<option value="{{ $driver }}">{{ ucfirst(str_replace('_', ' ', $driver)) }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Label *</label><input type="text" name="label" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Category *</label>
                        <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="card">Card / International</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="aggregator">Aggregator</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Priority</label><input type="number" name="priority" value="0" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" class="w-4 h-4 rounded text-maroon-600"> Active</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add</button><button type="button" onclick="closeModal('addGatewayModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
