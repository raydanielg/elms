<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Services\CommissionEngine;
use App\Services\InstructorLevelService;
use Illuminate\Http\Request;

class EarningsDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['teacher', 'solo_teacher', 'admin', 'super_admin'])) abort(403);
        $wallet = Wallet::getOrCreateForUser(auth()->id());
        $transactions = Transaction::where('instructor_id', auth()->id())
            ->with('course', 'user')
            ->latest()->paginate(20);
        $withdrawals = Withdrawal::where('user_id', auth()->id())->latest()->limit(10)->get();

        $levelProgress = null;
        if (auth()->user()->hasRole(['teacher', 'solo_teacher'])) {
            $levelProgress = app(InstructorLevelService::class)->getProgress(auth()->user());
        }

        $monthlyEarnings = Transaction::where('instructor_id', auth()->id())
            ->where('type', 'course_purchase')
            ->where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(net_amount) as total')
            ->groupBy('year', 'month')
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(12)->get();

        return view('earnings.dashboard', compact('wallet', 'transactions', 'withdrawals', 'levelProgress', 'monthlyEarnings'));
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payout_method' => 'required|in:mobile_money,bank_transfer,paypal',
            'payout_account' => 'required|string',
        ]);

        $wallet = Wallet::getOrCreateForUser(auth()->id());
        if ((float)$wallet->balance < (float)$validated['amount']) {
            return back()->with('error', 'Insufficient available balance.');
        }

        $minThreshold = config('elms.min_withdrawal', 10);
        if ((float)$validated['amount'] < $minThreshold) {
            return back()->with('error', "Minimum withdrawal amount is {$minThreshold}.");
        }

        $withdrawal = Withdrawal::create([
            'wallet_id' => $wallet->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'status' => 'pending',
            'payout_method' => $validated['payout_method'],
            'payout_account' => $validated['payout_account'],
        ]);

        $wallet->debit((float)$validated['amount']);

        return back()->with('success', 'Withdrawal request submitted.');
    }

    public function processWithdrawal(Request $request, Withdrawal $withdrawal)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,processed',
            'notes' => 'nullable|string',
        ]);

        $withdrawal->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        if ($validated['status'] === 'rejected') {
            $wallet = Wallet::find($withdrawal->wallet_id);
            $wallet->credit((float)$withdrawal->amount);
        }

        return response()->json(['message' => 'Withdrawal ' . $validated['status']]);
    }

    public function withdrawals()
    {
        if (auth()->user()->hasRole(['super_admin', 'admin'])) {
            $withdrawals = Withdrawal::with('user', 'processor')->latest()->paginate(20);
        } else {
            $withdrawals = Withdrawal::where('user_id', auth()->id())->latest()->paginate(20);
        }
        return view('earnings.withdrawals', compact('withdrawals'));
    }

    public function transactionLedger()
    {
        $query = Transaction::where('instructor_id', auth()->id())->with('course', 'user', 'coupon');
        if (request('status')) $query->where('status', request('status'));
        $transactions = $query->latest()->paginate(30);
        return view('earnings.ledger', compact('transactions'));
    }
}
