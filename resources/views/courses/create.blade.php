@extends('layouts.dashboard')

@section('page_title', 'Create Course')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="animate-slide-down">
        <h2 class="text-2xl font-bold text-gray-800">Create New Course</h2>
        <p class="text-sm text-gray-500 mt-1">Fill in the details below to create your course</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax data-reset-on-success="true" action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Course Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 focus:ring-2 focus:ring-maroon-100 outline-none transition-all" placeholder="e.g. Introduction to Web Development">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 focus:ring-2 focus:ring-maroon-100 outline-none transition-all" placeholder="What will students learn?"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Category</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Level *</label>
                    <select name="level" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Visibility *</label>
                    <select name="visibility" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                        <option value="private">Private (Institution only)</option>
                        @if(auth()->user()->isSoloTeacher())
                        <option value="marketplace">Marketplace (Public)</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Price ($)</label>
                    <input type="number" name="price" step="0.01" min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none" placeholder="0 for free">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Compare Price ($)</label>
                    <input type="number" name="compare_price" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none" placeholder="Original price for discount">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Language</label>
                    <input type="text" name="language" value="English" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Duration (hours)</label>
                    <input type="number" name="duration_hours" min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold hover:file:bg-maroon-100">
                </div>
                <div class="sm:col-span-2 flex items-center gap-6">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="drip_enabled" class="w-4 h-4 rounded text-maroon-600 focus:ring-maroon-300"> Enable Drip Content
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="has_certificate" checked class="w-4 h-4 rounded text-maroon-600 focus:ring-maroon-300"> Has Certificate
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Create Course</button>
                <a href="{{ route('courses.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
