@extends('layouts.dashboard')

@section('page_title', 'Course Versions')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Course Versions</h2><p class="text-sm text-gray-500 mt-1">{{ $course->title }} — version history</p></div>
        <button data-action-url="{{ route('course-versions.create', $course) }}" data-action-method="POST" data-confirm="Create a new version snapshot?" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Version</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Version</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Created By</th><th class="px-5 py-3 text-left font-bold">Created</th><th class="px-5 py-3 text-left font-bold">Published</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($versions as $version)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-bold text-gray-800">v{{ $version->version_number }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $version->status === 'published' ? 'bg-success-100 text-success-700' : ($version->status === 'draft' ? 'bg-warning-100 text-warning-700' : 'bg-gray-100 text-gray-500') }}">{{ ucfirst($version->status) }}</span></td>
                        <td class="px-5 py-3 text-gray-500">{{ $version->creator->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $version->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $version->published_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('course-versions.show', [$course, $version]) }}" class="text-xs font-bold text-maroon-500">View</a>
                                @if($version->status === 'draft')
                                <button data-action-url="{{ route('course-versions.publish', [$course, $version]) }}" data-action-method="POST" data-confirm="Publish this version? It will replace the current live version." class="text-xs font-bold text-success-600">Publish</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No versions yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
