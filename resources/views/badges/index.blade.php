@extends('layouts.dashboard')

@section('page_title', 'Badges')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Badges</h2><p class="text-sm text-gray-500 mt-1">Define badges and their earning rules</p></div>
        <div class="flex gap-2">
            <a href="{{ route('badges.trophy-case') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Trophy Case</a>
            <button onclick="openModal('addBadgeModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Badge</button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($badges as $badge)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background: {{ $badge->color }}20">
                    @if($badge->icon_image)<img src="{{ asset('storage/' . $badge->icon_image) }}" class="w-12 h-12 rounded-xl object-cover">@else<span style="color: {{ $badge->color }}">{{ $badge->icon ?? '🏆' }}</span>@endif
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $badge->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $badge->is_active ? 'Active' : 'Off' }}</span>
            </div>
            <h3 class="font-bold text-gray-800">{{ $badge->name }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ ucfirst($badge->category) }} · +{{ $badge->xp_reward }} XP</p>
            @if($badge->description)<p class="text-sm text-gray-500 mt-2">{{ $badge->description }}</p>@endif
            @if($badge->rules->isNotEmpty())
            <div class="mt-3 space-y-1">
                @foreach($badge->rules as $rule)
                <p class="text-xs text-gray-400">⚡ {{ $rule->trigger_event }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-2 mt-4">
                <button onclick="addRule({{ $badge->id }})" class="text-xs font-bold text-info-500">+ Rule</button>
                <form action="{{ route('badges.destroy', $badge) }}" method="POST" data-confirm="Delete this badge?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No badges defined yet</p></div>
        @endforelse
    </div>

    <div id="addBadgeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Create Badge</h3>
            <form data-ajax data-close-modal="addBadgeModal" action="{{ route('badges.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Category *</label>
                        <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="milestone">Milestone</option><option value="skill">Skill</option>
                            <option value="engagement">Engagement</option><option value="community">Community</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Icon (emoji)</label><input type="text" name="icon" placeholder="🏆" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Color</label><input type="color" name="color" value="#F6891F" class="w-full h-10 rounded-lg border border-gray-200 cursor-pointer"></div>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">XP Reward</label><input type="number" name="xp_reward" value="0" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Custom Icon Image</label><input type="file" name="icon_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold"></div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addBadgeModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
