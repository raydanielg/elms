@extends('layouts.dashboard')

@section('page_title', 'Transcript')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <a href="{{ route('transcripts.index') }}" class="text-sm text-maroon-500 font-bold">← Back to Transcripts</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Academic Transcript</h2>
        <p class="text-sm text-gray-500 mt-1">Verification Code: {{ $transcript->verification_code }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-slide-up">
        <div class="text-center mb-6 pb-6 border-b-2 border-maroon-100">
            <h1 class="text-2xl font-extrabold text-maroon-800">{{ $transcript->data_snapshot['tenant_name'] ?? 'ELMS' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Official Academic Transcript</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><p class="text-xs font-bold text-gray-400 uppercase">Student Name</p><p class="font-semibold text-gray-800">{{ $transcript->data_snapshot['student_name'] ?? '—' }}</p></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase">Date Issued</p><p class="font-semibold text-gray-800">{{ $transcript->issued_at?->format('F d, Y') ?? '—' }}</p></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase">Grading Scale</p><p class="font-semibold text-gray-800">{{ ucfirst($transcript->grading_scale) }}</p></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase">Status</p><span class="text-xs font-bold px-2 py-1 rounded-full {{ $transcript->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($transcript->status) }}</span></div>
        </div>

        <h3 class="font-bold text-gray-800 mb-3">Completed Courses</h3>
        <table class="w-full text-sm border border-gray-100 rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr><th class="px-4 py-2 text-left">Course</th><th class="px-4 py-2 text-left">Score</th><th class="px-4 py-2 text-left">Completed</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($transcript->data_snapshot['courses'] ?? [] as $course)
                <tr><td class="px-4 py-2 text-gray-700">{{ $course['title'] }}</td><td class="px-4 py-2 font-semibold">{{ $course['score'] }}%</td><td class="px-4 py-2 text-xs text-gray-400">{{ $course['completed_at'] ?? '—' }}</td></tr>
                @endforeach
            </tbody>
        </table>

        @if(!empty($transcript->data_snapshot['certificates']))
        <h3 class="font-bold text-gray-800 mt-6 mb-3">Certificates Earned</h3>
        <div class="space-y-2">
            @foreach($transcript->data_snapshot['certificates'] as $cert)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div><p class="text-sm font-semibold text-gray-700">{{ $cert['title'] }}</p><p class="text-xs text-gray-400">{{ $cert['number'] }}</p></div>
                <p class="text-xs text-gray-400">{{ $cert['issued_at'] ?? '—' }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400">Verification Code</p>
                <p class="font-mono font-bold text-maroon-600">{{ $transcript->verification_code }}</p>
            </div>
            <a href="{{ route('transcripts.verify', $transcript->verification_code) }}" target="_blank" class="px-4 py-2 bg-maroon-50 text-maroon-700 rounded-lg text-xs font-bold">Public Verification Page</a>
        </div>
    </div>
</div>
@endsection
