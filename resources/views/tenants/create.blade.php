@extends('layouts.dashboard')

@section('page_title', 'Create Tenant')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Create Tenant</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('tenants.store') }}" method="POST">
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
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Plan</label>
                        <select name="plan_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="">Select plan</option>
                            @foreach($plans as $plan)<option value="{{ $plan->id }}">{{ $plan->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Contact Email</label><input type="email" name="contact_email" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Contact Phone</label><input type="text" name="contact_phone" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Address</label><textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Domain</label><input type="text" name="domain" placeholder="example.elms.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Create Tenant</button>
                <a href="{{ route('tenants.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
