@extends('layouts.dashboard')

@section('page_title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between animate-slide-down">
        <div><h2 class="text-2xl font-bold text-gray-800">Audit Logs</h2><p class="text-sm text-gray-500 mt-1">Track all system actions</p></div>
        <a href="{{ route('audit-logs.export') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Export CSV</a>
    </div>

    <form method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 animate-slide-up">
        <input type="text" name="action" placeholder="Action" value="{{ request('action') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
        <input type="text" name="module" placeholder="Module" value="{{ request('module') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
        <input type="date" name="from" value="{{ request('from') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
        <button type="submit" class="col-span-full px-5 py-2 bg-maroon-600 text-white rounded-xl font-bold text-sm">Filter</button>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Date</th><th class="px-5 py-3 text-left font-bold">User</th><th class="px-5 py-3 text-left font-bold">Action</th><th class="px-5 py-3 text-left font-bold">Module</th><th class="px-5 py-3 text-left font-bold">IP</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $log->action }}</td>
                        <td class="px-5 py-3 text-gray-400">{{ $log->module ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No audit logs found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center">{{ $logs->links() }}</div>
</div>
@endsection
