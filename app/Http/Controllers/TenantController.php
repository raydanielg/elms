<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['plan', 'users'])->latest()->paginate(15);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:institution,solo',
            'plan_id' => 'nullable|exists:plans,id',
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'domain' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['status'] = 'trialing';
        $validated['trial_ends_at'] = now()->addDays(14);

        Tenant::create($validated);

        return response()->json(['message' => 'Tenant created successfully!', 'redirect' => route('tenants.index')]);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'users', 'courses']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:institution,solo',
            'plan_id' => 'nullable|exists:plans,id',
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'domain' => 'nullable|string',
            'status' => 'required|in:active,suspended,trialing,cancelled',
        ]);

        $tenant->update($validated);
        return response()->json(['message' => 'Tenant updated successfully!']);
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return response()->json(['message' => 'Tenant deleted successfully!']);
    }
}

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->paginate(15);
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:institution,solo',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_teachers' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:0',
            'max_courses' => 'nullable|integer|min:0',
            'storage_limit_gb' => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);

        Plan::create($validated);

        return response()->json(['message' => 'Plan created successfully!', 'redirect' => route('plans.index')]);
    }

    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:institution,solo',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_teachers' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:0',
            'max_courses' => 'nullable|integer|min:0',
            'storage_limit_gb' => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $plan->update($validated);
        return response()->json(['message' => 'Plan updated successfully!']);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => 'Plan deleted successfully!']);
    }
}
