<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Transaction;
use App\Services\CommissionEngine;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $refunds = Refund::with('transaction', 'user', 'processor')->latest()->paginate(20);
        return view('refunds.index', compact('refunds'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reason' => 'required|string',
        ]);

        $transaction = Transaction::findOrFail($validated['transaction_id']);
        if ($transaction->status === 'refunded') {
            return back()->with('error', 'This transaction has already been refunded.');
        }

        app(CommissionEngine::class)->processRefund($transaction, $validated['reason'], auth()->id());
        return back()->with('success', 'Refund processed successfully.');
    }
}
