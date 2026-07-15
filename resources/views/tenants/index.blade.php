@extends('layouts.dashboard')

@section('page_title', 'Tenants')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Tenants</h2><p class="text-sm text-gray-500 mt-1">Manage all institutions and solo teachers</p></div>
        <a href="{{ route('tenants.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Tenant</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($tenants as $tenant)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-maroon-500 to-maroon-700 flex items-center justify-center text-white font-bold text-lg">{{ strtoupper(substr($tenant->name, 0, 1)) }}</div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $tenant->status === 'active' ? 'bg-success-100 text-success-700' : ($tenant->status === 'trialing' ? 'bg-warning-100 text-warning-700' : 'bg-danger-100 text-danger-700') }}">{{ ucfirst($tenant->status) }}</span>
            </div>
            <h3 class="font-bold text-gray-800">{{ $tenant->name }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ ucfirst($tenant->type) }} · {{ $tenant->plan?->name ?? 'No Plan' }}</p>
            <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                <span>{{ $tenant->users->count() }} users</span>
                <span>{{ $tenant->courses->count() }} courses</span>
            </div>
            <div class="flex gap-2 mt-4">
                <a href="{{ route('tenants.show', $tenant) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100 transition-all">View</a>
                <a href="{{ route('tenants.edit', $tenant) }}" class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold">Edit</a>
                <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" data-confirm="Delete this tenant?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
            <p class="text-gray-400 font-semibold">No tenants yet</p>
        </div>
        @endforelse
    </div>
    <div class="flex justify-center">{{ $tenants->links() }}</div>
</div>
@endsection
