@extends('layouts.dashboard')

@section('page_title', 'Transaction Ledger')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Transaction Ledger</h2><p class="text-sm text-gray-500 mt-1">Full breakdown of every sale and commission</p></div>

    <div class="flex gap-2 animate-slide-down">
        <a href="{{ route('earnings.ledger') }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ !request('status') ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">All</a>
        @foreach(['completed', 'pending', 'refunded'] as $status)
        <a href="{{ route('earnings.ledger', ['status' => $status]) }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ request('status') === $status ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($status) }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold">Date</th>
                        <th class="px-4 py-3 text-left font-bold">Course</th>
                        <th class="px-4 py-3 text-left font-bold">Student</th>
                        <th class="px-4 py-3 text-right font-bold">Gross</th>
                        <th class="px-4 py-3 text-right font-bold">Commission</th>
                        <th class="px-4 py-3 text-right font-bold">Gateway</th>
                        <th class="px-4 py-3 text-right font-bold">Net</th>
                        <th class="px-4 py-3 text-left font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-400">{{ $tx->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $tx->course->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $tx->user->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format((float)$tx->gross_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right text-danger-500">-{{ number_format((float)$tx->commission_amount, 2) }} <span class="text-xs text-gray-400">({{ $tx->commission_rate_applied }}%)</span></td>
                        <td class="px-4 py-3 text-right text-gray-400">-{{ number_format((float)$tx->gateway_fee, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-success-600">{{ number_format((float)$tx->net_amount, 2) }}</td>
                        <td class="px-4 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $tx->status === 'completed' ? 'bg-success-100 text-success-700' : $tx->status === 'refunded' ? 'bg-danger-100 text-danger-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($tx->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No transactions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $transactions->links() }}</div>
</div>
@endsection
