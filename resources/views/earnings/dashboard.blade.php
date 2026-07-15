@extends('layouts.dashboard')

@section('page_title', 'Earnings Dashboard')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Earnings Dashboard</h2><p class="text-sm text-gray-500 mt-1">Your income, wallet, and withdrawal overview</p></div>

    {{-- Wallet Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 stagger">
        <div class="bg-gradient-to-br from-success-500 to-success-700 rounded-2xl p-5 text-white card-sm">
            <p class="text-sm text-success-100">Available Balance</p>
            <p class="text-3xl font-extrabold mt-1">{{ number_format((float)$wallet->balance, 2) }} {{ $wallet->currency }}</p>
        </div>
        <div class="bg-gradient-to-br from-warning-500 to-warning-700 rounded-2xl p-5 text-white card-sm">
            <p class="text-sm text-warning-100">Pending Balance</p>
            <p class="text-3xl font-extrabold mt-1">{{ number_format((float)$wallet->pending_balance, 2) }} {{ $wallet->currency }}</p>
        </div>
        <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-5 text-white card-sm">
            <p class="text-sm text-maroon-100">Lifetime Earnings</p>
            <p class="text-3xl font-extrabold mt-1">{{ number_format((float)$wallet->total_earned, 2) }} {{ $wallet->currency }}</p>
        </div>
        <div class="bg-gradient-to-br from-info-500 to-info-700 rounded-2xl p-5 text-white card-sm">
            <p class="text-sm text-info-100">Total Withdrawn</p>
            <p class="text-3xl font-extrabold mt-1">{{ number_format((float)$wallet->total_withdrawn, 2) }} {{ $wallet->currency }}</p>
        </div>
    </div>

    {{-- Level Progress --}}
    @if($levelProgress && $levelProgress['current'])
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-400">Current Instructor Level</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-2xl">{{ $levelProgress['current']->badge_icon ?? '🏆' }}</span>
                    <p class="text-xl font-extrabold text-gray-800">{{ $levelProgress['current']->name }}</p>
                    <span class="text-sm font-bold text-maroon-600 bg-maroon-50 px-2 py-0.5 rounded-full">{{ $levelProgress['current']->commission_rate }}% commission</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">Total Sales</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $levelProgress['total_sales'] }}</p>
                <p class="text-xs text-gray-400">Avg Rating: {{ $levelProgress['avg_rating'] }}</p>
            </div>
        </div>
        @if($levelProgress['next'])
        <div>
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progress to {{ $levelProgress['next']->name }} ({{ $levelProgress['next']->commission_rate }}% commission)</span>
                <span>{{ $levelProgress['sales_remaining'] }} more sales @if($levelProgress['rating_remaining'] > 0) · +{{ $levelProgress['rating_remaining'] }} rating @endif</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-maroon-500 to-secondary-500 rounded-full transition-all duration-500" style="width: {{ $levelProgress['progress'] }}%"></div>
            </div>
        </div>
        @else
        <p class="text-sm text-success-600 font-bold">Maximum level reached — Elite Instructor!</p>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Recent Sales</h3>
                <a href="{{ route('earnings.ledger') }}" class="text-xs font-bold text-maroon-500">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($transactions as $tx)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $tx->course->title ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $tx->user->name ?? '—' }} · {{ $tx->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-300 mt-0.5">Commission: {{ $tx->commission_rate_applied }}% · Net: {{ number_format((float)$tx->net_amount, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success-600">{{ number_format((float)$tx->net_amount, 2) }}</p>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $tx->status === 'completed' ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($tx->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">No sales yet</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Withdrawals --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Recent Withdrawals</h3>
                <button onclick="openModal('withdrawalModal')" class="text-xs font-bold text-maroon-500">Request Withdrawal</button>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($withdrawals as $w)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ number_format((float)$w->amount, 2) }} {{ $wallet->currency }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $w->payout_method)) }} · {{ $w->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $w->status === 'processed' ? 'bg-success-100 text-success-700' : $w->status === 'pending' ? 'bg-warning-100 text-warning-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($w->status) }}</span>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">No withdrawals yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Withdrawal Modal --}}
    <div id="withdrawalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Request Withdrawal</h3>
            <form action="{{ route('earnings.withdrawals.request') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Amount ({{ $wallet->currency }}) *</label><input type="number" name="amount" step="0.01" min="10" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" placeholder="Available: {{ number_format((float)$wallet->balance, 2) }}"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Payout Method *</label>
                        <select name="payout_method" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Account Details *</label><input type="text" name="payout_account" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" placeholder="Phone number / Account number / Email"></div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Submit Request</button><button type="button" onclick="closeModal('withdrawalModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
