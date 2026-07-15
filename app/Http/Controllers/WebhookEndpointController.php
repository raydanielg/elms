<?php

namespace App\Http\Controllers;

use App\Models\WebhookEndpoint;
use App\Models\WebhookDispatch;
use Illuminate\Http\Request;

class WebhookEndpointController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $endpoints = WebhookEndpoint::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')->latest()->get();
        return view('webhooks.index', compact('endpoints'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'url' => 'required|url',
            'events' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['secret'] = \Illuminate\Support\Str::random(32);
        WebhookEndpoint::create($validated);
        return response()->json(['message' => 'Webhook endpoint created']);
    }

    public function update(Request $request, WebhookEndpoint $endpoint)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $endpoint->update($request->only(['url', 'events', 'is_active']));
        return response()->json(['message' => 'Webhook endpoint updated']);
    }

    public function destroy(WebhookEndpoint $endpoint)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $endpoint->delete();
        return response()->json(['message' => 'Webhook endpoint removed']);
    }

    public function logs()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $logs = WebhookDispatch::whereHas('endpoint', fn($q) =>
            $q->where('tenant_id', auth()->user()->tenant_id)->orWhereNull('tenant_id')
        )->latest()->paginate(50);
        return view('webhooks.logs', compact('logs'));
    }
}
