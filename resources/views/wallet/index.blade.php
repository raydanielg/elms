@extends('layouts.dashboard')

@section('page_title', 'Wallet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Wallet</h2><p class="text-sm text-gray-500 mt-1">Manage your earnings and withdrawals</p></div>

    {{-- Balance Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 stagger">
        <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-6 text-white card-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <p class="text-sm text-maroon-100">Available Balance</p>
                <p class="text-3xl font-extrabold mt-2">${{ number_format($wallet->balance, 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Total Earned</p>
            <p class="text-3xl font-extrabold text-success-600 mt-2">${{ number_format($wallet->total_earned, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Total Withdrawn</p>
            <p class="text-3xl font-extrabold text-gray-600 mt-2">${{ number_format($wallet->total_withdrawn, 2) }}</p>
        </div>
    </div>

    {{-- Withdrawal Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">Request Withdrawal</h3>
        <form data-ajax data-reset-on-success="true" action="{{ route('wallet.withdraw') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Amount ($) *</label><input type="number" name="amount" required min="1" step="0.01" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Payout Method *</label>
                    <select name="payout_method" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="bank">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Account Details *</label><input type="text" name="payout_account" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none" placeholder="Account number / phone / email"></div>
            </div>
            <button type="submit" class="mt-4 px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Request Withdrawal</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Recent Transactions</h3></div>
            <div class="divide-y divide-gray-50">
                @forelse($transactions as $tx)
                <div class="flex items-center gap-3 p-4">
                    <div class="w-9 h-9 rounded-xl {{ $tx->type === 'credit' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tx->type === 'credit' ? 'M12 4v16m8-8H4' : 'M20 12H4' }}"/></svg>
                    </div>
                    <div class="flex-1"><p class="text-sm font-semibold text-gray-800">{{ $tx->description }}</p><p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p></div>
                    <span class="font-bold text-sm {{ $tx->type === 'credit' ? 'text-success-600' : 'text-danger-600' }}">{{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}</span>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">No transactions yet</div>
                @endforelse
            </div>
        </div>

        {{-- Withdrawals --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Withdrawal History</h3></div>
            <div class="divide-y divide-gray-50">
                @forelse($withdrawals as $wd)
                <div class="flex items-center gap-3 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">${{ number_format($wd->amount, 2) }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($wd->payout_method) }} · {{ $wd->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $wd->status === 'approved' ? 'bg-success-100 text-success-700' : ($wd->status === 'rejected' ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700') }}">{{ ucfirst($wd->status) }}</span>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">No withdrawals yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
