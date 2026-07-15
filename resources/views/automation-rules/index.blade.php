@extends('layouts.dashboard')

@section('page_title', 'Automation Rules')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Automation Rules</h2><p class="text-sm text-gray-500 mt-1">If this, then that — automate workflows</p></div>
        <button onclick="openModal('addRuleModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Rule</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 stagger">
        @forelse($rules as $rule)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-bold text-gray-800">{{ $rule->name }}</h3>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $rule->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $rule->is_active ? 'Active' : 'Off' }}</span>
            </div>
            @if($rule->description)<p class="text-sm text-gray-500">{{ $rule->description }}</p>@endif
            <div class="mt-3 space-y-1 text-xs">
                <p class="text-gray-400">When: <span class="font-bold text-gray-700">{{ $triggers[$rule->trigger_event] ?? $rule->trigger_event }}</span></p>
                <p class="text-gray-400">Then: <span class="font-bold text-gray-700">{{ $actions[$rule->action_class] ?? class_basename($rule->action_class) }}</span></p>
            </div>
            <div class="flex gap-2 mt-4">
                <form action="{{ route('automation-rules.destroy', $rule) }}" method="POST" data-confirm="Delete this rule?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No automation rules yet</p></div>
        @endforelse
    </div>

    <div id="addRuleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Create Automation Rule</h3>
            <form data-ajax data-close-modal="addRuleModal" action="{{ route('automation-rules.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Trigger *</label>
                        <select name="trigger_event" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            @foreach($triggers as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Action *</label>
                        <select name="action_class" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            @foreach($actions as $class => $label)<option value="{{ $class }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-maroon-600"> Active</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addRuleModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
