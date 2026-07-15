@extends('layouts.dashboard')

@section('page_title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Edit Tenant</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('tenants.update', $tenant) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required value="{{ $tenant->name }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Type *</label>
                        <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="institution" {{ $tenant->type === 'institution' ? 'selected' : '' }}>Institution</option>
                            <option value="solo" {{ $tenant->type === 'solo' ? 'selected' : '' }}>Solo Teacher</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $tenant->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="trialing" {{ $tenant->status === 'trialing' ? 'selected' : '' }}>Trialing</option>
                            <option value="cancelled" {{ $tenant->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Plan</label>
                        <select name="plan_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="">Select plan</option>
                            @foreach($plans as $plan)<option value="{{ $plan->id }}" {{ $tenant->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Domain</label><input type="text" name="domain" value="{{ $tenant->domain }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">{{ $tenant->description }}</textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Contact Email</label><input type="email" name="contact_email" value="{{ $tenant->contact_email }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Contact Phone</label><input type="text" name="contact_phone" value="{{ $tenant->contact_phone }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Address</label><textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">{{ $tenant->address }}</textarea></div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Save Changes</button>
                <a href="{{ route('tenants.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
