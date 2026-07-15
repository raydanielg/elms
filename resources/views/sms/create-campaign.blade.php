@extends('layouts.dashboard')

@section('page_title', 'New SMS Campaign')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Create SMS Campaign</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('sms.campaigns.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Title *</label><input type="text" name="title" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Message *</label><textarea name="message" required rows="4" maxlength="160" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none" oninput="document.getElementById('charCount').textContent = this.value.length + '/160'"></textarea><p id="charCount" class="text-xs text-gray-400 mt-1">0/160</p></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Filter by Course (optional)</label>
                    <select name="recipient_filters[course_id]" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="">All students</option>
                        @foreach(auth()->user()->tenant ? auth()->user()->tenant->courses : \App\Models\Course::where('owner_id', auth()->id())->get() as $course)<option value="{{ $course->id }}">{{ $course->title }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Send Campaign</button>
                <a href="{{ route('sms.campaigns') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
