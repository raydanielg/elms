@extends('layouts.dashboard')

@section('page_title', 'Certificates')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">My Certificates</h2><p class="text-sm text-gray-500 mt-1">Your earned certificates of completion</p></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 stagger">
        @forelse($certificates as $cert)
            <div class="bg-gradient-to-br from-maroon-50 via-white to-orange-50 rounded-2xl shadow-sm border border-maroon-100 p-6 card-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-400/5 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-maroon-500 to-maroon-700 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Certificate #</p>
                            <p class="font-bold text-gray-800 text-sm">{{ $cert->certificate_number }}</p>
                        </div>
                    </div>
                    <h3 class="font-bold text-gray-800">{{ $cert->course->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Score: {{ $cert->final_score }}% · Issued: {{ $cert->created_at->format('M d, Y') }}</p>
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('certificates.show', $cert) }}" class="flex-1 text-center px-4 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm hover:bg-maroon-700">View Certificate</a>
                        <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl font-bold text-sm">Verify</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4"/></svg>
                <p class="text-gray-400 font-semibold">No certificates yet</p>
                <p class="text-gray-300 text-sm mt-1">Complete a course to earn your first certificate</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
