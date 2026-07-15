@extends('layouts.app', ['title' => 'Verify Certificate'])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-maroon-50 to-orange-50 p-4">
    <div class="max-w-2xl w-full">
        @if($certificate)
        <div class="bg-white rounded-2xl shadow-lg border-2 border-maroon-200 p-8 animate-scale-in text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-success-400 to-success-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-success-700">Certificate Verified ✓</h2>
            <p class="text-gray-500 mt-2">This certificate is valid and authentic.</p>
            <div class="mt-6 text-left space-y-2">
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Holder</span><span class="font-bold text-gray-800">{{ $certificate->user->name }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Course</span><span class="font-bold text-gray-800">{{ $certificate->course->title }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Score</span><span class="font-bold text-gray-800">{{ $certificate->final_score }}%</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Issued</span><span class="font-bold text-gray-800">{{ $certificate->created_at->format('M d, Y') }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Certificate #</span><span class="font-bold text-gray-800">{{ $certificate->certificate_number }}</span></div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg border-2 border-danger-200 p-8 text-center animate-scale-in">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-danger-400 to-danger-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-danger-700">Invalid Certificate</h2>
            <p class="text-gray-500 mt-2">The verification code could not be found. This certificate may be invalid or revoked.</p>
        </div>
        @endif
    </div>
</div>
@endsection
