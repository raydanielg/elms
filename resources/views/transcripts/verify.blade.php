@extends('layouts.app', ['title' => 'Verify Transcript'])

@section('page_title', 'Verify Transcript')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="max-w-lg w-full">
        @if($isValid && $transcript)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-success-100 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.044z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-800">Transcript Verified</h1>
            <p class="text-sm text-gray-500 mt-2">This is a valid academic transcript issued by ELMS.</p>
            <div class="mt-6 text-left space-y-3">
                <div><p class="text-xs font-bold text-gray-400 uppercase">Student</p><p class="font-semibold text-gray-800">{{ $transcript->data_snapshot['student_name'] ?? '—' }}</p></div>
                <div><p class="text-xs font-bold text-gray-400 uppercase">Institution</p><p class="font-semibold text-gray-800">{{ $transcript->data_snapshot['tenant_name'] ?? 'ELMS' }}</p></div>
                <div><p class="text-xs font-bold text-gray-400 uppercase">Date Issued</p><p class="font-semibold text-gray-800">{{ $transcript->issued_at?->format('F d, Y') ?? '—' }}</p></div>
                <div><p class="text-xs font-bold text-gray-400 uppercase">Courses Completed</p><p class="font-semibold text-gray-800">{{ count($transcript->data_snapshot['courses'] ?? []) }}</p></div>
                <div><p class="text-xs font-bold text-gray-400 uppercase">Status</p><span class="text-xs font-bold px-2 py-1 rounded-full bg-success-100 text-success-700">Valid</span></div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-danger-100 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-800">Verification Failed</h1>
            <p class="text-sm text-gray-500 mt-2">@if($transcript)This transcript has been revoked or archived.@else The verification code was not found.@endif</p>
        </div>
        @endif
        <p class="text-center text-xs text-gray-400 mt-4">Powered by ELMS — Secure Academic Verification</p>
    </div>
</div>
@endsection
