@extends('layouts.dashboard')

@section('page_title', 'Edit Course')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <h2 class="text-2xl font-bold text-gray-800">Edit Course</h2>
        <p class="text-sm text-gray-500 mt-1">Update course details</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Course Title *</label>
                    <input type="text" name="title" required value="{{ $course->title }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">{{ $course->description }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Category</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $course->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Level *</label>
                    <select name="level" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="beginner" {{ $course->level === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ $course->level === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ $course->level === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Status *</label>
                    <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="draft" {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ $course->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="pending_review" {{ $course->status === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Visibility *</label>
                    <select name="visibility" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="private" {{ $course->visibility === 'private' ? 'selected' : '' }}>Private</option>
                        <option value="marketplace" {{ $course->visibility === 'marketplace' ? 'selected' : '' }}>Marketplace</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Price ($)</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ $course->price }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Language</label>
                    <input type="text" name="language" value="{{ $course->language }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Duration (hours)</label>
                    <input type="number" name="duration_hours" min="0" value="{{ $course->duration_hours }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold">
                </div>
                <div class="sm:col-span-2 flex items-center gap-6">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="drip_enabled" {{ $course->drip_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"> Drip Content</label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="has_certificate" {{ $course->has_certificate ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"> Certificate</label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Save Changes</button>
                <a href="{{ route('courses.show', $course) }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
