@extends('layouts.dashboard')

@section('page_title', 'My Transcripts')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Transcripts</h2><p class="text-sm text-gray-500 mt-1">Your academic record</p></div>
        <form action="{{ route('transcripts.generate') }}" method="POST" data-confirm="Generate a new transcript?">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Generate Transcript</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="divide-y divide-gray-50">
            @forelse($transcripts as $transcript)
            <div class="p-5 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-800">Transcript #{{ $transcript->verification_code }}</p>
                        <p class="text-xs text-gray-400 mt-1">Issued: {{ $transcript->issued_at?->format('M d, Y') ?? '—' }}</p>
                        <p class="text-xs text-gray-400">Grading: {{ ucfirst($transcript->grading_scale) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $transcript->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($transcript->status) }}</span>
                        <a href="{{ route('transcripts.show', $transcript) }}" class="text-xs font-bold text-maroon-500">View</a>
                        @if($transcript->status === 'active')
                        <form action="{{ route('transcripts.destroy', $transcript) }}" method="POST" data-confirm="Archive this transcript?" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-danger-500">Archive</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-400">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="font-semibold">No transcripts yet</p>
                <p class="text-xs mt-1">Generate a transcript to compile your academic record.</p>
            </div>
            @endforelse
        </div>
    </div>
    <div class="flex justify-center">{{ $transcripts->links() }}</div>
</div>
@endsection
