@extends('layouts.dashboard')

@section('page_title', 'SMS Campaigns')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">SMS Campaigns</h2><p class="text-sm text-gray-500 mt-1">Bulk SMS to students</p></div>
        <a href="{{ route('sms.campaigns.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Campaign</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Title</th><th class="px-5 py-3 text-left font-bold">Recipients</th><th class="px-5 py-3 text-left font-bold">Sent</th><th class="px-5 py-3 text-left font-bold">Delivered</th><th class="px-5 py-3 text-left font-bold">Failed</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($campaigns as $campaign)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $campaign->title }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $campaign->total_recipients }}</td>
                        <td class="px-5 py-3 text-info-600">{{ $campaign->sent_count }}</td>
                        <td class="px-5 py-3 text-success-600">{{ $campaign->delivered_count }}</td>
                        <td class="px-5 py-3 text-danger-600">{{ $campaign->failed_count }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $campaign->status === 'completed' ? 'bg-success-100 text-success-700' : ($campaign->status === 'failed' ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700') }}">{{ ucfirst($campaign->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $campaign->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No campaigns yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $campaigns->links() }}</div>
</div>
@endsection
