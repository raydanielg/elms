<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $coupons = Coupon::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')->latest()->paginate(20);
        return view('coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'course_ids' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $validated['tenant_id'] = auth()->user()->tenant_id;
        Coupon::create($validated);
        return response()->json(['message' => 'Coupon created']);
    }

    public function update(Request $request, Coupon $coupon)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $coupon->update($request->only(['type', 'value', 'min_purchase', 'usage_limit', 'starts_at', 'expires_at', 'course_ids', 'is_active']));
        return response()->json(['message' => 'Coupon updated']);
    }

    public function destroy(Coupon $coupon)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted']);
    }

    public function validateCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon || !$coupon->isValid($request->amount, $request->course_id)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
        }
        $discount = $coupon->calculateDiscount($request->amount);
        return response()->json(['valid' => true, 'discount' => $discount, 'final_amount' => $request->amount - $discount]);
    }
}
