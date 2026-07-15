@extends('layouts.dashboard')

@section('page_title', 'Version Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <a href="{{ route('course-versions.index', $course) }}" class="text-sm text-maroon-500 font-bold">← Back to Versions</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Version {{ $version->version_number }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 stagger">
        <div class="bg-white rounded-xl p-4 border border-gray-100 card-sm"><p class="text-xs text-gray-400">Status</p><p class="font-bold text-gray-800 mt-1">{{ ucfirst($version->status) }}</p></div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 card-sm"><p class="text-xs text-gray-400">Created By</p><p class="font-bold text-gray-800 mt-1">{{ $version->creator->name }}</p></div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 card-sm"><p class="text-xs text-gray-400">Created</p><p class="font-bold text-gray-800 mt-1 text-sm">{{ $version->created_at->format('M d, Y') }}</p></div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 card-sm"><p class="text-xs text-gray-400">Published</p><p class="font-bold text-gray-800 mt-1 text-sm">{{ $version->published_at?->format('M d, Y') ?? '—' }}</p></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">Content Snapshot</h3>
        <div class="space-y-3">
            <div><p class="text-xs font-bold text-gray-400 uppercase">Title</p><p class="text-gray-700">{{ $version->content_snapshot['title'] ?? '—' }}</p></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase">Description</p><p class="text-sm text-gray-600">{{ $version->content_snapshot['description'] ?? '—' }}</p></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase mt-4">Modules</p>
                @foreach($version->content_snapshot['modules'] ?? [] as $module)
                <div class="border-l-2 border-maroon-200 pl-3 py-1">
                    <p class="font-semibold text-gray-700 text-sm">{{ $module['title'] }}</p>
                    @foreach($module['lessons'] ?? [] as $lesson)<p class="text-xs text-gray-400 ml-3">• {{ $lesson['title'] }}</p>@endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
