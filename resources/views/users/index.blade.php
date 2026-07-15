@extends('layouts.dashboard')

@section('page_title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Users</h2><p class="text-sm text-gray-500 mt-1">Manage all platform users</p></div>
        <a href="{{ route('users.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New User</a>
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-slide-up">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
            <select name="role" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="solo_teacher" {{ request('role') === 'solo_teacher' ? 'selected' : '' }}>Solo Teacher</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
            </select>
            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">User</th>
                        <th class="px-5 py-3 text-left font-bold">Role</th>
                        <th class="px-5 py-3 text-left font-bold">Tenant</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-left font-bold">Joined</th>
                        <th class="px-5 py-3 text-left font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div><p class="font-semibold text-gray-800">{{ $user->name }}</p><p class="text-xs text-gray-400">{{ $user->email }}</p></div>
                            </div>
                        </td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $user->role === 'super_admin' ? 'bg-maroon-100 text-maroon-700' : 'bg-gray-100 text-gray-600' }}">{{ $user->role_label }}</span></td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->tenant?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $user->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">{{ ucfirst($user->status) }}</span></td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1">
                                <a href="{{ route('users.show', $user) }}" class="text-xs font-bold text-maroon-500 hover:text-maroon-700">View</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-xs font-bold text-gray-500 hover:text-gray-700">Edit</a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" data-confirm="Delete this user?" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-danger-500 hover:text-danger-700">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No users found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
