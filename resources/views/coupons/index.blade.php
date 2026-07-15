@extends('layouts.dashboard')

@section('page_title', 'Coupons')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Coupons</h2><p class="text-sm text-gray-500 mt-1">Discount codes for courses</p></div>
        <button onclick="openModal('addCouponModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Coupon</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Code</th><th class="px-5 py-3 text-left font-bold">Type</th><th class="px-5 py-3 text-left font-bold">Value</th><th class="px-5 py-3 text-left font-bold">Used</th><th class="px-5 py-3 text-left font-bold">Expires</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-bold text-maroon-600">{{ $coupon->code }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ ucfirst($coupon->type) }}</td>
                        <td class="px-5 py-3 font-semibold">{{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . number_format($coupon->value, 2) }}</td>
                        <td class="px-5 py-3 text-gray-400">{{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $coupon->expires_at?->format('M d, Y') ?? 'Never' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $coupon->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $coupon->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-5 py-3">
                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" data-confirm="Delete this coupon?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No coupons yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $coupons->links() }}</div>

    <div id="addCouponModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Create Coupon</h3>
            <form data-ajax data-close-modal="addCouponModal" action="{{ route('coupons.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Code *</label><input type="text" name="code" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm uppercase"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Type *</label><select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"><option value="percentage">Percentage</option><option value="fixed">Fixed Amount</option></select></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Value *</label><input type="number" name="value" required step="0.01" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Usage Limit</label><input type="number" name="usage_limit" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Min Purchase</label><input type="number" name="min_purchase" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Starts At</label><input type="date" name="starts_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Expires At</label><input type="date" name="expires_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-maroon-600"> Active</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addCouponModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
