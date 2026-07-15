<?php

namespace App\Http\Controllers;

use App\Models\Transcript;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\VerificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TranscriptController extends Controller
{
    public function index()
    {
        $transcripts = Transcript::where('user_id', auth()->id())->latest()->paginate(20);
        return view('transcripts.index', compact('transcripts'));
    }

    public function generate()
    {
        $userId = auth()->id();
        $tenantId = auth()->user()->tenant_id;

        $existing = Transcript::where('user_id', $userId)->where('status', 'active')->first();
        if ($existing) return redirect()->route('transcripts.show', $existing)->with('info', 'You already have an active transcript.');

        $enrollments = Enrollment::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('course')->get();

        $certificates = Certificate::where('user_id', $userId)->where('status', 'valid')->get();

        $data = [
            'student_name' => auth()->user()->name,
            'student_email' => auth()->user()->email,
            'tenant_name' => auth()->user()->tenant?->name,
            'courses' => $enrollments->map(fn($e) => [
                'title' => $e->course->title,
                'score' => (float)$e->final_score,
                'completed_at' => $e->completed_at?->format('Y-m-d'),
            ])->toArray(),
            'certificates' => $certificates->map(fn($c) => [
                'number' => $c->certificate_number,
                'title' => $c->title,
                'issued_at' => $c->issued_at?->format('Y-m-d'),
            ])->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];

        $transcript = Transcript::create([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'verification_code' => Transcript::generateCode(),
            'status' => 'active',
            'grading_scale' => 'percentage',
            'data_snapshot' => $data,
            'issued_at' => now(),
        ]);

        $transcript->update(['data_hash' => $transcript->generateDataHash()]);

        return redirect()->route('transcripts.show', $transcript)->with('success', 'Transcript generated successfully.');
    }

    public function show(Transcript $transcript)
    {
        if ($transcript->user_id !== auth()->id() && !auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        return view('transcripts.show', compact('transcript'));
    }

    public function verify($code)
    {
        $transcript = Transcript::where('verification_code', $code)->first();
        $isValid = $transcript && $transcript->status === 'active';

        if ($transcript) {
            VerificationLog::log($code, Transcript::class, $transcript->id, $isValid);
        }

        return view('transcripts.verify', compact('transcript', 'isValid'));
    }

    public function destroy(Transcript $transcript)
    {
        if ($transcript->user_id !== auth()->id()) abort(403);
        $transcript->update(['status' => 'archived']);
        return response()->json(['message' => 'Transcript archived']);
    }
}
