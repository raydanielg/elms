@extends('layouts.dashboard')

@section('page_title', 'Refunds')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Refunds</h2><p class="text-sm text-gray-500 mt-1">Process and track refund requests</p></div>
        <button onclick="openModal('addRefundModal')" class="px-5 py-2.5 bg-gradient-to-r from-danger-500 to-danger-700 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Process Refund</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Transaction</th><th class="px-5 py-3 text-left font-bold">Student</th><th class="px-5 py-3 text-right font-bold">Amount</th><th class="px-5 py-3 text-left font-bold">Reason</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($refunds as $refund)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-maroon-600">{{ $refund->transaction->transaction_reference ?? '#' . $refund->transaction_id }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $refund->user->name }}</td>
                        <td class="px-5 py-3 text-right font-bold text-danger-500">{{ number_format((float)$refund->amount, 2) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $refund->reason ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $refund->status === 'completed' ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">{{ ucfirst($refund->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $refund->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No refunds processed</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $refunds->links() }}</div>

    <div id="addRefundModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Process Refund</h3>
            <form action="{{ route('refunds.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Transaction ID *</label>
                        <input type="number" name="transaction_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" placeholder="Enter transaction ID">
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Reason *</label><textarea name="reason" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" placeholder="Reason for refund"></textarea></div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-danger-600 text-white rounded-xl font-bold text-sm">Process Refund</button><button type="button" onclick="closeModal('addRefundModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
