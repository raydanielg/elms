<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $currencies = Currency::orderBy('code')->get();
        return view('currencies.index', compact('currencies'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string',
            'symbol' => 'required|string',
            'locale' => 'nullable|string',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        Currency::create($validated);
        return response()->json(['message' => 'Currency added']);
    }

    public function update(Request $request, Currency $currency)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $currency->update($request->only(['name', 'symbol', 'locale', 'exchange_rate', 'is_active']));
        return response()->json(['message' => 'Currency updated']);
    }

    public function destroy(Currency $currency)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $currency->delete();
        return response()->json(['message' => 'Currency removed']);
    }
}
