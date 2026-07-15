@extends('layouts.dashboard')

@section('page_title', 'Webhook Logs')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><a href="{{ route('payment-gateways.index') }}" class="text-sm text-maroon-500 font-bold">← Back to Gateways</a><h2 class="text-2xl font-bold text-gray-800 mt-2">Payment Webhook Logs</h2></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Gateway</th><th class="px-5 py-3 text-left font-bold">Reference</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ ucfirst($log->gateway) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $log->transaction_reference ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $log->status === 'processed' ? 'bg-success-100 text-success-700' : ($log->status === 'failed' ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700') }}">{{ ucfirst($log->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No webhook logs</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $logs->links() }}</div>
</div>
@endsection
