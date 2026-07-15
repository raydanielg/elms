<?php

namespace App\Http\Controllers;

use App\Models\WebhookEndpoint;
use App\Models\WebhookDispatch;
use Illuminate\Http\Request;

class WebhookEndpointController extends Controller
{
    public function index()
    {
        $this->authorize('adminOrAbove');
        $endpoints = WebhookEndpoint::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')->latest()->get();
        return view('webhooks.index', compact('endpoints'));
    }

    public function store(Request $request)
    {
        $this->authorize('adminOrAbove');
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
        $this->authorize('adminOrAbove');
        $endpoint->update($request->only(['url', 'events', 'is_active']));
        return response()->json(['message' => 'Webhook endpoint updated']);
    }

    public function destroy(WebhookEndpoint $endpoint)
    {
        $this->authorize('adminOrAbove');
        $endpoint->delete();
        return response()->json(['message' => 'Webhook endpoint removed']);
    }

    public function logs()
    {
        $this->authorize('adminOrAbove');
        $logs = WebhookDispatch::whereHas('endpoint', fn($q) =>
            $q->where('tenant_id', auth()->user()->tenant_id)->orWhereNull('tenant_id')
        )->latest()->paginate(50);
        return view('webhooks.logs', compact('logs'));
    }
}
