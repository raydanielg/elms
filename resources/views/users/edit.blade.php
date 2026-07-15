@extends('layouts.dashboard')

@section('page_title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Edit User</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required value="{{ $user->name }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Email *</label><input type="email" name="email" required value="{{ $user->email }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Role *</label>
                        <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="solo_teacher" {{ $user->role === 'solo_teacher' ? 'selected' : '' }}>Solo Teacher</option>
                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Tenant</label>
                        <select name="tenant_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="">None</option>
                            @foreach($tenants as $t)<option value="{{ $t->id }}" {{ $user->tenant_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">New Password (leave blank to keep)</label><input type="password" name="password" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Phone</label><input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Bio</label><textarea name="bio" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">{{ $user->bio }}</textarea></div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Save Changes</button>
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
