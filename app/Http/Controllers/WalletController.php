<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallet = Wallet::firstOrCreate(['user_id' => auth()->id()]);
        $transactions = Transaction::where('user_id', auth()->id())->latest()->paginate(10);
        $withdrawals = Withdrawal::where('user_id', auth()->id())->latest()->paginate(10);
        return view('wallet.index', compact('wallet', 'transactions', 'withdrawals'));
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payout_method' => 'required|string',
            'payout_account' => 'required|string',
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => auth()->id()]);

        if ($validated['amount'] > $wallet->balance) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        Withdrawal::create([
            'wallet_id' => $wallet->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'payout_method' => $validated['payout_method'],
            'payout_account' => $validated['payout_account'],
            'status' => 'pending',
        ]);

        $wallet->decrement('balance', $validated['amount']);

        return response()->json(['message' => 'Withdrawal request submitted successfully!']);
    }
}

class TransactionController extends Controller
{
    public function index()
    {
        $query = Transaction::with(['user', 'tenant']);

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('user_id', auth()->id())
                  ->orWhere('tenant_id', auth()->user()->tenant_id);
        }

        $transactions = $query->latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }
}

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with(['user', 'processor'])->latest()->paginate(15);
        return view('withdrawals.index', compact('withdrawals'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        $withdrawal->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        $withdrawal->wallet->increment('total_withdrawn', $withdrawal->amount);

        return response()->json(['message' => 'Withdrawal approved successfully!']);
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'notes' => $request->input('reason'),
        ]);

        $withdrawal->wallet->increment('balance', $withdrawal->amount);

        return response()->json(['message' => 'Withdrawal rejected. Amount returned to wallet.']);
    }
}
