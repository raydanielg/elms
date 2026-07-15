@extends('layouts.app', ['title' => 'Verify Certificate'])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-maroon-50 to-orange-50 p-4">
    <div class="max-w-2xl w-full">
        @if($isValid && $certificate)
        <div class="bg-white rounded-2xl shadow-lg border-2 border-maroon-200 p-8 animate-scale-in text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-success-400 to-success-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-success-700">Certificate Verified</h2>
            <p class="text-gray-500 mt-2">This certificate is valid and authentic.</p>
            <div class="mt-6 text-left space-y-2">
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Holder</span><span class="font-bold text-gray-800">{{ $certificate->user->name }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Course</span><span class="font-bold text-gray-800">{{ $certificate->course->title }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Score</span><span class="font-bold text-gray-800">{{ $certificate->final_score }}%</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Issued</span><span class="font-bold text-gray-800">{{ $certificate->issued_at?->format('M d, Y') ?? $certificate->created_at->format('M d, Y') }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Certificate #</span><span class="font-bold text-gray-800">{{ $certificate->certificate_number }}</span></div>
                <div class="flex justify-between p-3 bg-success-50 rounded-xl"><span class="text-success-600">Status</span><span class="font-bold text-success-700">Valid</span></div>
            </div>
        </div>
        @elseif($certificate && $certificate->isRevoked())
        <div class="bg-white rounded-2xl shadow-lg border-2 border-danger-200 p-8 text-center animate-scale-in">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-danger-400 to-danger-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-danger-700">Certificate Revoked</h2>
            <p class="text-gray-500 mt-2">This certificate has been revoked and is no longer valid.</p>
            <div class="mt-6 text-left space-y-2">
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Holder</span><span class="font-bold text-gray-800">{{ $certificate->user->name }}</span></div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span class="text-gray-400">Course</span><span class="font-bold text-gray-800">{{ $certificate->course->title }}</span></div>
                <div class="flex justify-between p-3 bg-danger-50 rounded-xl"><span class="text-danger-600">Status</span><span class="font-bold text-danger-700">REVOKED</span></div>
                @if($certificate->revocation_reason)<div class="p-3 bg-gray-50 rounded-xl"><p class="text-xs text-gray-400">Reason</p><p class="text-sm text-gray-600">{{ $certificate->revocation_reason }}</p></div>@endif
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
        <p class="text-center text-xs text-gray-400 mt-4">Powered by ELMS — Secure Certificate Verification</p>
    </div>
</div>
@endsection
