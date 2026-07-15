<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with('course.owner')
            ->latest()
            ->paginate(12);
        return view('enrollments.index', compact('enrollments'));
    }

    public function destroy(Enrollment $enrollment)
    {
        if ($enrollment->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $enrollment->delete();
        return response()->json(['message' => 'Enrollment cancelled successfully!']);
    }
}

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course')
            ->latest()
            ->get();
        return view('certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('user', 'course');
        return view('certificates.show', compact('certificate'));
    }

    public function verify($code)
    {
        $certificate = Certificate::where('verification_code', $code)->with('user', 'course')->first();
        return view('certificates.verify', compact('certificate'));
    }

    public function generate(Course $course)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        if (!$enrollment || $enrollment->status !== 'completed') {
            return response()->json(['message' => 'You must complete the course first.'], 422);
        }

        $existing = Certificate::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        if ($existing) {
            return response()->json(['message' => 'Certificate already exists.', 'redirect' => route('certificates.show', $existing)]);
        }

        $cert = Certificate::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'certificate_number' => 'ELMS-' . date('Y') . '-' . str_pad(Certificate::count() + 1, 5, '0', STR_PAD_LEFT),
            'verification_code' => Str::uuid()->toString(),
            'final_score' => $enrollment->progress,
        ]);

        return response()->json(['message' => 'Certificate generated successfully!', 'redirect' => route('certificates.show', $cert)]);
    }
}
