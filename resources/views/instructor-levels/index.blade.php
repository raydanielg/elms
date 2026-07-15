@extends('layouts.dashboard')

@section('page_title', 'Instructor Levels')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Instructor Levels</h2><p class="text-sm text-gray-500 mt-1">Tier system with commission rates and perks</p></div>
        <div class="flex gap-2">
            @if(auth()->user()->isSuperAdmin())
            <button onclick="openModal('addLevelModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Level</button>
            @endif
            <a href="{{ route('instructor-levels.progress') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">My Progress</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($levels as $level)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background: {{ $level->badge_color }}20">
                    <span style="color: {{ $level->badge_color }}">{{ $level->badge_icon ?? '🏆' }}</span>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $level->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $level->is_active ? 'Active' : 'Off' }}</span>
            </div>
            <h3 class="font-bold text-gray-800">Level {{ $level->level_number }} — {{ $level->name }}</h3>
            <div class="mt-3 space-y-1 text-xs text-gray-500">
                <p>Min Sales: <span class="font-bold text-gray-700">{{ $level->min_sales }}</span></p>
                <p>Min Rating: <span class="font-bold text-gray-700">{{ $level->min_rating }}</span></p>
                <p>Commission: <span class="font-bold text-maroon-600">{{ $level->commission_rate }}%</span></p>
                <p>Payout Speed: <span class="font-bold text-gray-700">{{ $level->payout_speed_days }} days</span></p>
            </div>
            @if($level->perks)
            <div class="mt-3 pt-3 border-t border-gray-50">
                @foreach($level->perks as $perk)<p class="text-xs text-gray-400">✓ {{ $perk }}</p>@endforeach
            </div>
            @endif
            @if(auth()->user()->isSuperAdmin())
            <div class="flex gap-2 mt-4">
                <button onclick="editLevel({{ $level->id }})" class="text-xs font-bold text-maroon-500">Edit</button>
                <form action="{{ route('instructor-levels.destroy', $level) }}" method="POST" data-confirm="Delete this level?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No instructor levels configured</p></div>
        @endforelse
    </div>

    @if(auth()->user()->isSuperAdmin())
    <div class="flex justify-center">
        <form action="{{ route('instructor-levels.recalculate') }}" method="POST" data-confirm="Recalculate all instructor levels?">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-info-50 text-info-700 rounded-xl font-bold text-sm">Recalculate All Instructor Levels</button>
        </form>
    </div>
    @endif

    <div id="addLevelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">New Instructor Level</h3>
            <form data-ajax data-close-modal="addLevelModal" action="{{ route('instructor-levels.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Level Number *</label><input type="number" name="level_number" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Min Sales *</label><input type="number" name="min_sales" required min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Min Rating *</label><input type="number" name="min_rating" required step="0.01" min="0" max="5" value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Commission % *</label><input type="number" name="commission_rate" required step="0.01" min="0" max="100" value="25" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Payout Speed (days) *</label><input type="number" name="payout_speed_days" required min="1" value="7" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Badge Icon</label><input type="text" name="badge_icon" placeholder="🏆" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Badge Color</label><input type="color" name="badge_color" value="#F6891F" class="w-full h-10 rounded-lg border border-gray-200 cursor-pointer"></div>
                    </div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addLevelModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
