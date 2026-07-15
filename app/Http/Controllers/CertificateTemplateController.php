<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $templates = CertificateTemplate::forTenant(auth()->user()->tenant_id);
        return view('certificate-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:course_completion,achievement,attendance,custom',
            'layout' => 'required|in:classic,modern,minimal,institutional',
            'font_family' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'show_grade' => 'boolean',
            'show_qr_code' => 'boolean',
            'show_signature' => 'boolean',
            'show_logo' => 'boolean',
            'background_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('certificate-templates', 'public');
        }

        $validated['tenant_id'] = auth()->user()->tenant_id;
        CertificateTemplate::create($validated);
        return response()->json(['message' => 'Template created']);
    }

    public function update(Request $request, CertificateTemplate $template)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $template->update($request->only([
            'name', 'layout', 'font_family', 'primary_color', 'secondary_color',
            'show_grade', 'show_qr_code', 'show_signature', 'show_logo', 'is_active'
        ]));
        return response()->json(['message' => 'Template updated']);
    }

    public function destroy(CertificateTemplate $template)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $template->delete();
        return response()->json(['message' => 'Template deleted']);
    }

    public function certificates()
    {
        $query = Certificate::with('user', 'course')->latest();
        if (auth()->user()->isSuperAdmin()) {
            // all
        } elseif (auth()->user()->hasRole(['admin'])) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        } else {
            $query->where('user_id', auth()->id());
        }
        $certificates = $query->paginate(20);
        return view('certificates.index', compact('certificates'));
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate(['revocation_reason' => 'required|string']);
        $certificate->revoke($validated['revocation_reason']);
        return response()->json(['message' => 'Certificate revoked']);
    }

    public function verify($code)
    {
        $certificate = Certificate::where('verification_code', $code)->first();
        $isValid = $certificate && $certificate->isValid();

        if ($certificate) {
            \App\Models\VerificationLog::log($code, Certificate::class, $certificate->id, $isValid);
        }

        return view('certificates.verify', compact('certificate', 'isValid'));
    }
}
