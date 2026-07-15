@extends('layouts.dashboard')

@section('page_title', 'Plans')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Subscription Plans</h2><p class="text-sm text-gray-500 mt-1">Manage pricing tiers</p></div>
        <a href="{{ route('plans.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Plan</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($plans as $plan)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 card-sm relative overflow-hidden">
            @if($plan->is_active)
            <div class="absolute top-0 right-0 w-24 h-24 bg-success-50 rounded-full -mr-12 -mt-12"></div>
            @endif
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $plan->name }}</h3>
                        <span class="text-xs text-gray-400">{{ ucfirst($plan->type) }}</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $plan->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="my-4">
                    <span class="text-3xl font-extrabold text-maroon-600">${{ number_format($plan->price_monthly, 2) }}</span>
                    <span class="text-sm text-gray-400">/month</span>
                </div>
                <p class="text-sm text-gray-500">{{ $plan->description }}</p>
                <div class="mt-4 space-y-1 text-xs text-gray-400">
                    @if($plan->max_teachers)<span class="block">Teachers: {{ $plan->max_teachers }}</span>@endif
                    @if($plan->max_students)<span class="block">Students: {{ $plan->max_students }}</span>@endif
                    @if($plan->max_courses)<span class="block">Courses: {{ $plan->max_courses }}</span>@endif
                    @if($plan->storage_limit_gb)<span class="block">Storage: {{ $plan->storage_limit_gb }}GB</span>@endif
                    @if($plan->commission_rate)<span class="block">Commission: {{ $plan->commission_rate }}%</span>@endif
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('plans.edit', $plan) }}" class="flex-1 text-center px-3 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold hover:bg-maroon-100">Edit</a>
                    <form action="{{ route('plans.destroy', $plan) }}" method="POST" data-confirm="Delete plan?" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-danger-50 text-danger-600 rounded-lg text-xs font-bold">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
            <p class="text-gray-400 font-semibold">No plans yet</p>
        </div>
        @endforelse
    </div>
    <div class="flex justify-center">{{ $plans->links() }}</div>
</div>
@endsection
