@extends('layouts.dashboard')

@section('page_title', 'Course Approvals')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Course Approvals</h2><p class="text-sm text-gray-500 mt-1">Review and approve course submissions</p></div>

    <div class="flex gap-2 animate-slide-down">
        <a href="{{ route('course-approvals.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ !request('status') ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">All</a>
        <a href="{{ route('course-approvals.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ request('status') === 'pending' ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">Pending</a>
        <a href="{{ route('course-approvals.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ request('status') === 'approved' ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">Approved</a>
        <a href="{{ route('course-approvals.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ request('status') === 'rejected' ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">Rejected</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Course</th><th class="px-5 py-3 text-left font-bold">Requested By</th><th class="px-5 py-3 text-left font-bold">Type</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Date</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($approvals as $approval)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $approval->course->title }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $approval->requestedBy->name }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $approval->approval_type === 'platform' ? 'bg-info-100 text-info-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($approval->approval_type) }}</span></td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $approval->status === 'approved' ? 'bg-success-100 text-success-700' : ($approval->status === 'rejected' ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700') }}">{{ ucfirst($approval->status) }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $approval->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            @if($approval->status === 'pending')
                            <div class="flex gap-2">
                                <button data-action-url="{{ route('course-approvals.approve', $approval) }}" data-action-method="POST" data-confirm="Approve this course?" class="text-xs font-bold text-success-600">Approve</button>
                                <button onclick="rejectApproval({{ $approval->id }})" class="text-xs font-bold text-danger-500">Reject</button>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">{{ $approval->reviewed_at?->format('M d') ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No approval requests</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $approvals->links() }}</div>
</div>
@endsection
