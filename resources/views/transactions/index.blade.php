@extends('layouts.dashboard')

@section('page_title', 'Transactions')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Transactions</h2><p class="text-sm text-gray-500 mt-1">All financial transactions</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">Description</th>
                        <th class="px-5 py-3 text-left font-bold">Type</th>
                        <th class="px-5 py-3 text-left font-bold">Amount</th>
                        <th class="px-5 py-3 text-left font-bold">User</th>
                        <th class="px-5 py-3 text-left font-bold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $tx->description }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $tx->type === 'credit' ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">{{ ucfirst($tx->type) }}</span></td>
                        <td class="px-5 py-3 font-bold {{ $tx->type === 'credit' ? 'text-success-600' : 'text-danger-600' }}">{{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $tx->user?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No transactions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $transactions->links() }}</div>
</div>
@endsection
