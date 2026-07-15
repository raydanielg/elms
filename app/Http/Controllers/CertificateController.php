<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\RecognitionEngine;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $query = Certificate::with('user', 'course')->latest();
        if (auth()->user()->isSuperAdmin()) {
            // all
        } elseif (auth()->user()->hasRole(['admin'])) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        } elseif (auth()->user()->hasRole(['teacher', 'solo_teacher'])) {
            $query->whereHas('course', fn($q) => $q->where('owner_id', auth()->id()));
        } else {
            $query->where('user_id', auth()->id());
        }
        $certificates = $query->paginate(20);
        return view('certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() && !auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        return view('certificates.show', compact('certificate'));
    }

    public function generate(Course $course)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);

        $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)->first();

        if (!$enrollment || $enrollment->status !== 'completed') {
            return back()->with('error', 'Course must be completed to generate a certificate.');
        }

        $cert = app(RecognitionEngine::class)->issueCertificate($enrollment);
        if (!$cert) {
            return back()->with('error', 'Certificate could not be generated.');
        }

        return redirect()->route('certificates.show', $cert)->with('success', 'Certificate generated!');
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

    public function revoke(Request $request, Certificate $certificate)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate(['revocation_reason' => 'required|string']);
        $certificate->revoke($validated['revocation_reason']);
        return response()->json(['message' => 'Certificate revoked']);
    }
}
