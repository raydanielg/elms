<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Models\NotificationTrigger;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $this->authorize('adminOrAbove');
        $templates = NotificationTemplate::orderBy('event')->get();
        $triggers = NotificationTrigger::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')->get();
        return view('notification-templates.index', compact('templates', 'triggers'));
    }

    public function store(Request $request)
    {
        $this->authorize('superAdminOnly');
        $validated = $request->validate([
            'key' => 'required|string|unique:notification_templates,key',
            'event' => 'required|string',
            'channel' => 'required|in:email,sms,in_app,push',
            'language' => 'string|size:2',
            'subject' => 'nullable|string',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);
        NotificationTemplate::create($validated);
        return response()->json(['message' => 'Template created']);
    }

    public function update(Request $request, NotificationTemplate $template)
    {
        $this->authorize('adminOrAbove');
        $template->update($request->only(['subject', 'body', 'is_active']));
        return response()->json(['message' => 'Template updated']);
    }

    public function updateTrigger(Request $request, string $event)
    {
        $this->authorize('adminOrAbove');
        $trigger = NotificationTrigger::firstOrCreate(
            ['event' => $event, 'tenant_id' => auth()->user()->tenant_id],
            ['email_enabled' => true, 'in_app_enabled' => true]
        );
        $trigger->update($request->only(['email_enabled', 'sms_enabled', 'in_app_enabled', 'push_enabled']));
        return response()->json(['message' => 'Trigger updated']);
    }
}
