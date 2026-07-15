<?php

namespace App\Http\Controllers;

use App\Models\SmsGateway;
use App\Models\SmsTemplate;
use App\Models\SmsCampaign;
use App\Models\SmsCredit;
use App\Models\SmsBundle;
use App\Models\User;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function gateways()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $gateways = SmsGateway::orderBy('priority')->get();
        $manager = app(SmsManager::class);
        $availableDrivers = $manager->availableDrivers() ?? ['africas_talking', 'beem_africa', 'twilio'];
        return view('sms.gateways', compact('gateways', 'availableDrivers'));
    }

    public function storeGateway(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $validated = $request->validate([
            'driver' => 'required|string|unique:sms_gateways,driver',
            'label' => 'required|string',
            'priority' => 'integer|min:0',
            'credentials' => 'nullable|array',
            'sender_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        SmsGateway::create($validated);
        return response()->json(['message' => 'SMS gateway added']);
    }

    public function updateGateway(Request $request, SmsGateway $gateway)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $gateway->update($request->only(['label', 'priority', 'credentials', 'sender_id', 'is_active']));
        return response()->json(['message' => 'SMS gateway updated']);
    }

    public function templates()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $templates = SmsTemplate::orderBy('event')->get();
        return view('sms.templates', compact('templates'));
    }

    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $template->update($request->only(['template', 'is_active', 'category']));
        return response()->json(['message' => 'SMS template updated']);
    }

    public function campaigns()
    {
        $campaigns = SmsCampaign::where('tenant_id', auth()->user()->tenant_id)
            ->orWhere('user_id', auth()->id())
            ->latest()->paginate(20);
        return view('sms.campaigns', compact('campaigns'));
    }

    public function createCampaign()
    {
        return view('sms.create-campaign');
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'recipient_filters' => 'nullable|array',
        ]);

        $credits = SmsCredit::forTenant(auth()->user()->tenant_id);

        $recipientCount = $this->estimateRecipients($validated['recipient_filters'] ?? []);
        if ($credits->balance < $recipientCount) {
            return back()->with('error', "Insufficient SMS credits. Need {$recipientCount}, have {$credits->balance}.");
        }

        $campaign = SmsCampaign::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'recipient_filters' => $validated['recipient_filters'] ?? null,
            'total_recipients' => $recipientCount,
            'status' => 'queued',
        ]);

        return redirect()->route('sms.campaigns')->with('success', 'Campaign queued for sending');
    }

    public function credits()
    {
        $credits = SmsCredit::forTenant(auth()->user()->tenant_id);
        $bundles = SmsBundle::where('is_active', true)->get();
        return view('sms.credits', compact('credits', 'bundles'));
    }

    public function purchaseCredits(Request $request)
    {
        $validated = $request->validate(['bundle_id' => 'required|exists:sms_bundles,id']);
        $bundle = SmsBundle::findOrFail($validated['bundle_id']);
        $credits = SmsCredit::forTenant(auth()->user()->tenant_id);
        $credits->add($bundle->credits);

        \App\Models\Transaction::create([
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
            'type' => 'sms_purchase',
            'amount' => $bundle->price,
            'status' => 'completed',
            'description' => "SMS Credits: {$bundle->name} ({$bundle->credits} credits)",
        ]);

        return response()->json(['message' => 'SMS credits purchased successfully']);
    }

    private function estimateRecipients(array $filters): int
    {
        $query = User::where('tenant_id', auth()->user()->tenant_id)->where('role', 'student');
        if (isset($filters['course_id'])) {
            $query->whereHas('enrollments', fn($q) => $q->where('course_id', $filters['course_id']));
        }
        return $query->count();
    }
}
