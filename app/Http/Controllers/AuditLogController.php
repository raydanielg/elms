<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $query = AuditLog::where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('action')) $query->where('action', 'like', "%{$request->action}%");
        if ($request->filled('module')) $query->where('module', $request->module);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);

        $logs = $query->latest()->paginate(50);
        return view('audit-logs.index', compact('logs'));
    }

    public function export()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $logs = AuditLog::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
        $filename = 'audit-logs-' . now()->format('Y-m-d') . '.csv';
        $headers = ['Content-Type' => 'text/csv'];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'User', 'Action', 'Module', 'IP Address']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->module ?? '',
                    $log->ip_address ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers + ['Content-Disposition' => "attachment; filename={$filename}"]);
    }
}
