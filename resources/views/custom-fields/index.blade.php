@extends('layouts.dashboard')

@section('page_title', 'Custom Fields')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Custom Fields</h2><p class="text-sm text-gray-500 mt-1">Add custom fields to forms</p></div>
        <button onclick="openModal('addFieldModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ Add Field</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Label</th><th class="px-5 py-3 text-left font-bold">Form</th><th class="px-5 py-3 text-left font-bold">Type</th><th class="px-5 py-3 text-left font-bold">Required</th><th class="px-5 py-3 text-left font-bold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($fields as $field)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-semibold text-gray-700">{{ $field->field_label }}</p><p class="text-xs text-gray-400 font-mono">{{ $field->field_name }}</p></td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ ucfirst(str_replace('_', ' ', $field->form_type)) }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-bold px-2 py-1 rounded-full bg-gray-100 text-gray-600">{{ ucfirst($field->field_type) }}</span></td>
                        <td class="px-5 py-3">{{ $field->is_required ? '<span class="text-danger-500 font-bold text-xs">Yes</span>' : '<span class="text-gray-400 text-xs">No</span>' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('custom-fields.destroy', $field) }}" method="POST" data-confirm="Delete this field?" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No custom fields</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addFieldModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
            <h3 class="font-bold text-gray-800 mb-4">Add Custom Field</h3>
            <form data-ajax data-close-modal="addFieldModal" action="{{ route('custom-fields.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Form Type *</label>
                        <select name="form_type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="registration">Registration</option>
                            <option value="profile">Profile</option>
                            <option value="course_application">Course Application</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Field Name *</label><input type="text" name="field_name" required placeholder="e.g. national_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Field Label *</label><input type="text" name="field_label" required placeholder="e.g. National ID Number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Field Type *</label>
                        <select name="field_type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="text">Text</option><option value="number">Number</option><option value="dropdown">Dropdown</option>
                            <option value="checkbox">Checkbox</option><option value="date">Date</option><option value="file">File Upload</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_required" class="w-4 h-4 rounded text-maroon-600"> Required</label>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Add</button><button type="button" onclick="closeModal('addFieldModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
