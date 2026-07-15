@extends('layouts.dashboard')

@section('page_title', 'Theme & Branding')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Theme & Branding</h2><p class="text-sm text-gray-500 mt-1">Customize your institution's look</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('theme.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Primary Color</label><input type="color" name="primary_color" value="{{ $theme->primary_color }}" class="w-full h-12 rounded-xl border border-gray-200 cursor-pointer"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Secondary Color</label><input type="color" name="secondary_color" value="{{ $theme->secondary_color }}" class="w-full h-12 rounded-xl border border-gray-200 cursor-pointer"></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Font Family</label><input type="text" name="font_family" value="{{ $theme->font_family }}" placeholder="e.g. Inter, Poppins" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Logo</label><input type="file" name="logo_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Favicon</label><input type="file" name="favicon_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:font-bold"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Custom Domain</label><input type="text" name="custom_domain" value="{{ $theme->custom_domain }}" placeholder="learn.myschool.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Email Sender Name</label><input type="text" name="email_sender_name" value="{{ $theme->email_sender_name }}" placeholder="My School" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Save Theme</button>
        </form>
    </div>
</div>
@endsection
