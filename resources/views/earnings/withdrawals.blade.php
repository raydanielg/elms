@extends('layouts.dashboard')

@section('page_title', 'Withdrawals')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Withdrawals</h2><p class="text-sm text-gray-500 mt-1">Withdrawal requests and processing</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">Instructor</th>
                        <th class="px-5 py-3 text-right font-bold">Amount</th>
                        <th class="px-5 py-3 text-left font-bold">Method</th>
                        <th class="px-5 py-3 text-left font-bold">Account</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-left font-bold">Date</th>
                        @if(auth()->user()->hasRole(['super_admin', 'admin']))<th class="px-5 py-3 text-left font-bold">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $w)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $w->user->name }}</td>
                        <td class="px-5 py-3 text-right font-bold">{{ number_format((float)$w->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ ucfirst(str_replace('_', ' ', $w->payout_method)) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $w->payout_account }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $w->status === 'processed' ? 'bg-success-100 text-success-700' : $w->status === 'pending' ? 'bg-warning-100 text-warning-700' : $w->status === 'rejected' ? 'bg-danger-100 text-danger-700' : 'bg-info-100 text-info-700' }}">{{ ucfirst($w->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $w->created_at->format('M d, Y') }}</td>
                        @if(auth()->user()->hasRole(['super_admin', 'admin']))
                        <td class="px-5 py-3">
                            @if($w->status === 'pending')
                            <button onclick="processWithdrawal({{ $w->id }}, 'approved')" class="text-xs font-bold text-success-600">Approve</button>
                            <button onclick="processWithdrawal({{ $w->id }}, 'rejected')" class="text-xs font-bold text-danger-500 ml-2">Reject</button>
                            @elseif($w->status === 'approved')
                            <button onclick="processWithdrawal({{ $w->id }}, 'processed')" class="text-xs font-bold text-info-600">Mark Processed</button>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->user()->hasRole(['super_admin', 'admin']) ? 7 : 6 }}" class="px-5 py-8 text-center text-gray-400">No withdrawals</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $withdrawals->links() }}</div>
</div>
@endsection
