@extends('layouts.dashboard')

@section('page_title', 'Withdrawals')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Withdrawal Requests</h2><p class="text-sm text-gray-500 mt-1">Review and process payout requests</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">User</th>
                        <th class="px-5 py-3 text-left font-bold">Amount</th>
                        <th class="px-5 py-3 text-left font-bold">Method</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-left font-bold">Date</th>
                        <th class="px-5 py-3 text-left font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $wd)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($wd->user->name, 0, 1)) }}</div>
                                <span class="font-semibold text-gray-700">{{ $wd->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-bold text-gray-800">${{ number_format($wd->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ ucfirst($wd->payout_method) }} · {{ $wd->payout_account }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $wd->status === 'approved' ? 'bg-success-100 text-success-700' : ($wd->status === 'rejected' ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700') }}">{{ ucfirst($wd->status) }}</span></td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $wd->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            @if($wd->status === 'pending')
                            <div class="flex gap-2">
                                <button data-action-url="{{ route('withdrawals.approve', $wd) }}" data-action-method="POST" data-confirm="Approve this withdrawal?" class="text-xs font-bold text-success-600 hover:text-success-800">Approve</button>
                                <button onclick="openModal('rejectModal{{ $wd->id }}')" class="text-xs font-bold text-danger-600 hover:text-danger-800">Reject</button>
                                <div id="rejectModal{{ $wd->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
                                        <h3 class="font-bold text-gray-800 mb-4">Reject Withdrawal</h3>
                                        <form data-ajax data-close-modal="rejectModal{{ $wd->id }}" action="{{ route('withdrawals.reject', $wd) }}" method="POST">
                                            @csrf
                                            <div><label class="block text-sm font-bold text-gray-700 mb-1">Reason</label><textarea name="reason" rows="3" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm"></textarea></div>
                                            <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-danger-600 text-white rounded-xl font-bold text-sm">Reject</button><button type="button" onclick="closeModal('rejectModal{{ $wd->id }}')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">{{ $wd->processor?->name ?? '' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No withdrawal requests</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $withdrawals->links() }}</div>
</div>
@endsection
