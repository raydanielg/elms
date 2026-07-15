@extends('layouts.dashboard')

@section('page_title', 'Revenue Shares')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Revenue Shares</h2><p class="text-sm text-gray-500 mt-1">Configure institution-teacher internal splits</p></div>
        <button onclick="openModal('addShareModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Add Revenue Share</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Teacher</th><th class="px-5 py-3 text-right font-bold">Institution %</th><th class="px-5 py-3 text-right font-bold">Teacher %</th><th class="px-5 py-3 text-left font-bold">Status</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shares as $share)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $share->teacher->name }}</td>
                        <td class="px-5 py-3 text-right font-bold text-maroon-600">{{ $share->institution_percentage }}%</td>
                        <td class="px-5 py-3 text-right font-bold text-success-600">{{ $share->teacher_percentage }}%</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full {{ $share->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $share->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-5 py-3">
                            <form action="{{ route('revenue-shares.destroy', $share) }}" method="POST" data-confirm="Remove this revenue share?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No revenue shares configured</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $shares->links() }}</div>

    <div id="addShareModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Add Revenue Share</h3>
            <form data-ajax data-close-modal="addShareModal" action="{{ route('revenue-shares.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Teacher *</label>
                        <select name="teacher_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            @php $teachers = \App\Models\User::where('role', 'teacher')->where('tenant_id', auth()->user()->tenant_id)->get(); @endphp
                            @foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Institution % *</label><input type="number" name="institution_percentage" required step="0.01" min="0" max="100" value="40" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Teacher % *</label><input type="number" name="teacher_percentage" required step="0.01" min="0" max="100" value="60" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    </div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addShareModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
