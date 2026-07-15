<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    public function index()
    {
        $this->authorize('adminOrAbove');
        $rules = AutomationRule::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')
            ->latest()->get();
        $triggers = [
            'enrollment.completed' => 'Course Completed',
            'enrollment.created' => 'New Enrollment',
            'quiz.submitted' => 'Quiz Submitted',
            'assignment.submitted' => 'Assignment Submitted',
            'user.inactive_14_days' => 'User Inactive 14 Days',
            'subscription.payment_failed' => 'Payment Failed',
            'payment.refunded' => 'Payment Refunded',
        ];
        $actions = [
            \App\Actions\Automation\GenerateCertificateAction::class => 'Generate Certificate',
            \App\Actions\Automation\SendNotificationAction::class => 'Send Notification',
            \App\Actions\Automation\SendReengagementAction::class => 'Send Re-engagement SMS/Email',
            \App\Actions\Automation\SuspendTenantAction::class => 'Suspend Tenant',
            \App\Actions\Automation\RevokeEnrollmentAction::class => 'Revoke Enrollment',
        ];
        return view('automation-rules.index', compact('rules', 'triggers', 'actions'));
    }

    public function store(Request $request)
    {
        $this->authorize('adminOrAbove');
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'trigger_event' => 'required|string',
            'conditions' => 'nullable|array',
            'action_class' => 'required|string',
            'action_params' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $validated['tenant_id'] = auth()->user()->tenant_id;
        AutomationRule::create($validated);
        return response()->json(['message' => 'Automation rule created']);
    }

    public function update(Request $request, AutomationRule $rule)
    {
        $this->authorize('adminOrAbove');
        $rule->update($request->only(['name', 'description', 'conditions', 'action_class', 'action_params', 'is_active']));
        return response()->json(['message' => 'Automation rule updated']);
    }

    public function destroy(AutomationRule $rule)
    {
        $this->authorize('adminOrAbove');
        $rule->delete();
        return response()->json(['message' => 'Automation rule deleted']);
    }
}
