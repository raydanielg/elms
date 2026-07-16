<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        if (auth()->user()->isSuperAdmin()) {
            $transactions = Transaction::with('user', 'tenant', 'course', 'instructor')->latest()->paginate(25);
        } elseif (auth()->user()->isAdmin()) {
            $transactions = Transaction::where('tenant_id', auth()->user()->tenant_id)
                ->with('user', 'course', 'instructor')->latest()->paginate(25);
        } else {
            $transactions = Transaction::where('user_id', auth()->id())
                ->with('course')->latest()->paginate(25);
        }
        return view('transactions.index', compact('transactions'));
    }
}
