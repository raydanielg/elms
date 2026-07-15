@extends('layouts.dashboard')

@section('page_title', 'Certificate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-maroon-200 p-12 animate-scale-in relative overflow-hidden">
        <div class="absolute top-0 left-0 w-40 h-40 bg-maroon-50 rounded-full -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-40 h-40 bg-orange-50 rounded-full -mr-20 -mb-20"></div>

        <div class="relative z-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-maroon-600 to-maroon-800 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>

            <p class="text-sm font-bold text-maroon-600 uppercase tracking-widest">Certificate of Completion</p>
            <div class="w-24 h-1 bg-gradient-to-r from-maroon-400 to-orange-400 mx-auto my-4 rounded-full"></div>

            <p class="text-gray-500 text-sm">This is to certify that</p>
            <h2 class="text-3xl font-extrabold text-gray-800 my-3">{{ $certificate->user->name }}</h2>
            <p class="text-gray-500 text-sm">has successfully completed the course</p>
            <h3 class="text-xl font-bold text-maroon-700 mt-2">{{ $certificate->course->title }}</h3>

            <div class="flex justify-center gap-12 mt-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Final Score</p>
                    <p class="text-lg font-bold text-gray-800">{{ $certificate->final_score }}%</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Issue Date</p>
                    <p class="text-lg font-bold text-gray-800">{{ $certificate->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-400">Certificate Number: {{ $certificate->certificate_number }}</p>
                <p class="text-xs text-gray-400">Verification Code: {{ $certificate->verification_code }}</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3 mt-6 justify-center">
        <a href="{{ route('certificates.verify', $certificate->verification_code) }}" target="_blank" class="px-5 py-2.5 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700">Verify Certificate</a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200">Print</button>
    </div>
</div>
@endsection
