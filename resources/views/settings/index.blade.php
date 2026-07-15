@extends('layouts.dashboard')

@section('page_title', 'Settings')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Settings</h2><p class="text-sm text-gray-500 mt-1">Application configuration</p></div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">General Settings</h3>
        <form data-ajax action="{{ route('settings.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">App Name</label><input type="text" name="app_name" value="{{ \App\Models\Setting::get('app_name', 'ELMS') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Timezone</label>
                    <select name="timezone" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="UTC" {{ \App\Models\Setting::get('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="Africa/Dar_es_Salaam" {{ \App\Models\Setting::get('timezone') === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar_es_Salaam</option>
                        <option value="Africa/Nairobi" {{ \App\Models\Setting::get('timezone') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                    </select>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Locale</label>
                    <select name="locale" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="en" {{ \App\Models\Setting::get('locale') === 'en' ? 'selected' : '' }}>English</option>
                        <option value="sw" {{ \App\Models\Setting::get('locale') === 'sw' ? 'selected' : '' }}>Swahili</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm hover:scale-105 transition-all">Save Settings</button>
        </form>
    </div>
</div>
@endsection
