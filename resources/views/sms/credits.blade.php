@extends('layouts.dashboard')

@section('page_title', 'SMS Credits')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">SMS Credits</h2><p class="text-sm text-gray-500 mt-1">Purchase and manage SMS balance</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 stagger">
        <div class="bg-gradient-to-br from-maroon-600 to-maroon-800 rounded-2xl p-6 text-white card-sm">
            <p class="text-sm text-maroon-100">Current Balance</p>
            <p class="text-3xl font-extrabold mt-2">{{ $credits->balance }}</p>
            <p class="text-xs text-maroon-200 mt-1">SMS credits</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Total Purchased</p>
            <p class="text-3xl font-extrabold text-success-600 mt-2">{{ $credits->total_purchased }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-sm">
            <p class="text-sm text-gray-400">Total Used</p>
            <p class="text-3xl font-extrabold text-gray-600 mt-2">{{ $credits->total_used }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">Purchase SMS Credits</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @forelse($bundles as $bundle)
            <div class="border border-gray-100 rounded-xl p-5 text-center hover:border-maroon-200 transition-all">
                <p class="font-bold text-gray-800">{{ $bundle->name }}</p>
                <p class="text-3xl font-extrabold text-maroon-600 mt-2">{{ $bundle->credits }}</p>
                <p class="text-xs text-gray-400">SMS credits</p>
                <p class="text-lg font-bold text-gray-700 mt-3">${{ number_format($bundle->price, 2) }}</p>
                <button data-action-url="{{ route('sms.credits.purchase') }}" data-action-method="POST" data-confirm="Purchase {{ $bundle->name }} for ${{ number_format($bundle->price, 2) }}?" class="mt-3 px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm w-full">
                    <form action="{{ route('sms.credits.purchase') }}" method="POST" class="hidden" data-ajax>
                        @csrf <input type="hidden" name="bundle_id" value="{{ $bundle->id }}">
                    </form>
                    Purchase
                </button>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-400 py-8">No SMS bundles available</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
