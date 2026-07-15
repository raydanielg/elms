@extends('layouts.dashboard')

@section('page_title', 'SMS Templates')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">SMS Templates</h2><p class="text-sm text-gray-500 mt-1">Edit message templates with placeholders</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Event</th><th class="px-5 py-3 text-left font-bold">Category</th><th class="px-5 py-3 text-left font-bold">Language</th><th class="px-5 py-3 text-left font-bold">Template</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Edit</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $template->event)) }}</p><p class="text-xs text-gray-400 font-mono">{{ $template->key }}</p></td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $template->category === 'critical' ? 'bg-danger-100 text-danger-700' : 'bg-info-100 text-info-700' }}">{{ ucfirst($template->category) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400 uppercase">{{ $template->language }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $template->template }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $template->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $template->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-5 py-3"><button onclick="editTemplate({{ $template->id }})" class="text-xs font-bold text-maroon-500">Edit</button></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No SMS templates</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
