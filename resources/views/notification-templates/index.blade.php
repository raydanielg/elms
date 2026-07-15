@extends('layouts.dashboard')

@section('page_title', 'Notification Templates')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Notification Templates & Triggers</h2><p class="text-sm text-gray-500 mt-1">Manage notification content and channel delivery</p></div>

    {{-- Trigger Matrix --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <h3 class="font-bold text-gray-800 mb-4">Notification Trigger Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Event</th><th class="px-5 py-3 text-center font-bold">Email</th><th class="px-5 py-3 text-center font-bold">SMS</th><th class="px-5 py-3 text-center font-bold">In-App</th><th class="px-5 py-3 text-center font-bold">Push</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $events = $templates->pluck('event')->unique(); @endphp
                    @foreach($events as $event)
                    @php $trigger = $triggers->where('event', $event)->first(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $event)) }}</td>
                        <td class="px-5 py-3 text-center"><input type="checkbox" data-action-url="{{ route('notification-triggers.update', $event) }}" data-action-method="PUT" name="email_enabled" {{ $trigger?->email_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"></td>
                        <td class="px-5 py-3 text-center"><input type="checkbox" data-action-url="{{ route('notification-triggers.update', $event) }}" data-action-method="PUT" name="sms_enabled" {{ $trigger?->sms_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"></td>
                        <td class="px-5 py-3 text-center"><input type="checkbox" data-action-url="{{ route('notification-triggers.update', $event) }}" data-action-method="PUT" name="in_app_enabled" {{ $trigger?->in_app_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"></td>
                        <td class="px-5 py-3 text-center"><input type="checkbox" data-action-url="{{ route('notification-triggers.update', $event) }}" data-action-method="PUT" name="push_enabled" {{ $trigger?->push_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Templates --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Templates</h3></div>
        <div class="divide-y divide-gray-50">
            @foreach($templates as $template)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between mb-1">
                    <p class="font-semibold text-gray-700 text-sm">{{ ucfirst(str_replace('_', ' ', $template->event)) }} <span class="text-xs text-gray-400">· {{ $template->channel }}</span></p>
                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $template->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}">{{ $template->is_active ? 'Active' : 'Off' }}</span>
                </div>
                @if($template->subject)<p class="text-xs font-bold text-gray-500">{{ $template->subject }}</p>@endif
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $template->body }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
