<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $withdrawals = Withdrawal::with('user', 'processor')->latest()->paginate(20);
        return view('withdrawals.index', compact('withdrawals'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $withdrawal->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);
        return response()->json(['message' => 'Withdrawal approved']);
    }

    public function reject(Withdrawal $withdrawal)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);
        $wallet = Wallet::find($withdrawal->wallet_id);
        if ($wallet) $wallet->credit((float)$withdrawal->amount);
        return response()->json(['message' => 'Withdrawal rejected']);
    }
}
