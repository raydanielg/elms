@extends('layouts.dashboard')

@section('page_title', 'Feature Flags')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Feature Flags</h2><p class="text-sm text-gray-500 mt-1">Enable or disable platform features globally or per plan</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Feature</th><th class="px-5 py-3 text-left font-bold">Description</th><th class="px-5 py-3 text-left font-bold">Global</th><th class="px-5 py-3 text-left font-bold">Plans</th><th class="px-5 py-3 text-left font-bold">Toggle</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($flags as $flag)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-semibold text-gray-800">{{ $flag->label }}</p><p class="text-xs text-gray-400 font-mono">{{ $flag->key }}</p></td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $flag->description ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $flag->is_global_enabled ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">{{ $flag->is_global_enabled ? 'Enabled' : 'Disabled' }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $flag->plan_ids ? count($flag->plan_ids) . ' plans' : 'All plans' }}</td>
                        <td class="px-5 py-3">
                            <button data-action-url="{{ route('feature-flags.toggle', $flag) }}" data-action-method="POST" data-confirm="Toggle this feature flag?" class="text-xs font-bold {{ $flag->is_global_enabled ? 'text-danger-500' : 'text-success-600' }}">{{ $flag->is_global_enabled ? 'Disable' : 'Enable' }}</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No feature flags configured</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
