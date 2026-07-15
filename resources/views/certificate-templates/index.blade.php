@extends('layouts.dashboard')

@section('page_title', 'Certificate Templates')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Certificate Templates</h2><p class="text-sm text-gray-500 mt-1">Design and manage certificate layouts</p></div>
        <button onclick="openModal('addTemplateModal')" class="px-5 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">+ New Template</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
        @forelse($templates as $template)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: {{ $template->primary_color }}20">
                    <svg class="w-6 h-6" style="color: {{ $template->primary_color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.044z"/></svg>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $template->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $template->is_active ? 'Active' : 'Off' }}</span>
            </div>
            <h3 class="font-bold text-gray-800">{{ $template->name }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ ucfirst($template->layout) }} · {{ ucfirst(str_replace('_', ' ', $template->type)) }}</p>
            <div class="flex gap-2 mt-3">
                <span class="w-5 h-5 rounded-full border-2 border-white shadow" style="background: {{ $template->primary_color }}"></span>
                <span class="w-5 h-5 rounded-full border-2 border-white shadow" style="background: {{ $template->secondary_color }}"></span>
            </div>
            <div class="flex gap-2 mt-4">
                <button onclick="editTemplate({{ $template->id }})" class="text-xs font-bold text-maroon-500">Edit</button>
                <form action="{{ route('certificate-templates.destroy', $template) }}" method="POST" data-confirm="Delete this template?" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-danger-500">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100"><p class="text-gray-400 font-semibold">No certificate templates yet</p></div>
        @endforelse
    </div>

    <div id="addTemplateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="modal-content bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-gray-800 mb-4">New Certificate Template</h3>
            <form data-ajax data-close-modal="addTemplateModal" action="{{ route('certificate-templates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Name *</label><input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Type *</label>
                        <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="course_completion">Course Completion</option>
                            <option value="achievement">Achievement</option>
                            <option value="attendance">Attendance</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Layout *</label>
                        <select name="layout" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                            <option value="classic">Classic</option><option value="modern">Modern</option>
                            <option value="minimal">Minimal</option><option value="institutional">Institutional</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Primary Color</label><input type="color" name="primary_color" value="#5A0917" class="w-full h-10 rounded-lg border border-gray-200 cursor-pointer"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Secondary Color</label><input type="color" name="secondary_color" value="#F6891F" class="w-full h-10 rounded-lg border border-gray-200 cursor-pointer"></div>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Font Family</label><input type="text" name="font_family" placeholder="Inter, Poppins" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Background Image</label><input type="file" name="background_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold"></div>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="show_grade" checked class="w-4 h-4 rounded text-maroon-600"> Show Grade</label>
                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="show_qr_code" checked class="w-4 h-4 rounded text-maroon-600"> QR Code</label>
                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="show_signature" checked class="w-4 h-4 rounded text-maroon-600"> Signature</label>
                        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="show_logo" checked class="w-4 h-4 rounded text-maroon-600"> Logo</label>
                    </div>
                </div>
                <div class="flex gap-3 mt-4"><button type="submit" class="px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Create</button><button type="button" onclick="closeModal('addTemplateModal')" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
