@extends('layouts.dashboard')

@section('page_title', 'Create Plan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Create Plan</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('plans.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Type *</label>
                        <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="institution">Institution</option>
                            <option value="solo">Solo Teacher</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Sort Order</label><input type="number" name="sort_order" min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Monthly Price ($) *</label><input type="number" name="price_monthly" required step="0.01" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Yearly Price ($) *</label><input type="number" name="price_yearly" required step="0.01" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Max Teachers</label><input type="number" name="max_teachers" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Max Students</label><input type="number" name="max_students" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Max Courses</label><input type="number" name="max_courses" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Storage (GB)</label><input type="number" name="storage_limit_gb" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Commission (%)</label><input type="number" name="commission_rate" min="0" max="100" step="0.01" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-maroon-600"> Active</label>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Create Plan</button>
                <a href="{{ route('plans.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
