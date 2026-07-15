@extends('layouts.dashboard')

@section('page_title', 'Awards')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Awards & Honors</h2><p class="text-sm text-gray-500 mt-1">Grant special recognition to students</p></div>
        <div class="flex gap-2">
            <a href="{{ route('awards.honor-roll') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Honor Roll</a>
            <button onclick="openModal('addAwardModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Grant Award</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Title</th><th class="px-5 py-3 text-left font-bold">Recipient</th><th class="px-5 py-3 text-left font-bold">Type</th><th class="px-5 py-3 text-left font-bold">Period</th><th class="px-5 py-3 text-left font-bold">Date</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($awards as $award)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-semibold text-gray-700">{{ $award->title }}</p>@if($award->description)<p class="text-xs text-gray-400">{{ $award->description }}</p>@endif</td>
                        <td class="px-5 py-3 text-gray-700">{{ $award->recipient->name }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full bg-maroon-100 text-maroon-700">{{ ucfirst(str_replace('_', ' ', $award->type)) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $award->period ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $award->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('awards.destroy', $award) }}" method="POST" data-confirm="Remove this award?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No awards granted yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $awards->links() }}</div>

    <div id="addAwardModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Grant Award</h3>
            <form data-ajax data-close-modal="addAwardModal" action="{{ route('awards.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Title *</label><input type="text" name="title" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Type *</label>
                        <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="student_of_month">Student of the Month</option>
                            <option value="top_performer">Top Performer</option>
                            <option value="most_improved">Most Improved</option>
                            <option value="perfect_attendance">Perfect Attendance</option>
                            <option value="instructor_recognition">Instructor Recognition</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Recipient *</label>
                        <select name="awarded_to" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            @php $students = \App\Models\User::where('role', 'student')->where('tenant_id', auth()->user()->tenant_id)->get(); @endphp
                            @foreach($students as $student)<option value="{{ $student->id }}">{{ $student->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Period</label><input type="text" name="period" placeholder="e.g. January 2025" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="issue_certificate" class="w-4 h-4 rounded text-maroon-600"> Issue certificate with this award</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_public" checked class="w-4 h-4 rounded text-maroon-600"> Show on Honor Roll</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Grant Award</button><button type="button" onclick="closeModal('addAwardModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
