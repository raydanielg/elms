@extends('layouts.dashboard')

@section('page_title', 'Referrals')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Referral Program</h2><p class="text-sm text-gray-500 mt-1">Earn commissions by sharing courses</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 stagger">
        <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-6 text-white card-sm">
            <p class="text-sm text-maroon-100">Total Earnings</p>
            <p class="text-3xl font-extrabold mt-2">${{ number_format($totalEarnings, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Total Referrals</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $referrals->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Pending</p>
            <p class="text-3xl font-extrabold text-warning-600 mt-2">{{ $referrals->where('status', 'pending')->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Code</th><th class="px-5 py-3 text-left font-bold">Course</th><th class="px-5 py-3 text-left font-bold">Referred</th><th class="px-5 py-3 text-left font-bold">Commission</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($referrals as $ref)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-bold text-maroon-600">{{ $ref->referral_code }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $ref->course?->title ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $ref->referred?->name ?? 'Pending' }}</td>
                        <td class="px-5 py-3 font-semibold">${{ number_format($ref->commission_amount, 2) }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $ref->status === 'completed' ? 'bg-success-100 text-success-700' : ($ref->status === 'pending' ? 'bg-warning-100 text-warning-700' : 'bg-gray-100 text-gray-500') }}">{{ ucfirst($ref->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $ref->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No referrals yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $referrals->links() }}</div>
</div>
@endsection
