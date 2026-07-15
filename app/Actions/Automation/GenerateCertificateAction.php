<?php

namespace App\Actions\Automation;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Str;

class GenerateCertificateAction implements AutomationActionInterface
{
    public function execute($model, array $params = []): void
    {
        if (!($model instanceof Enrollment) || $model->status !== 'completed') return;

        $exists = Certificate::where('user_id', $model->user_id)
            ->where('course_id', $model->course_id)->exists();
        if ($exists) return;

        Certificate::create([
            'user_id' => $model->user_id,
            'course_id' => $model->course_id,
            'certificate_number' => 'ELMS-' . date('Y') . '-' . str_pad(Certificate::count() + 1, 5, '0', STR_PAD_LEFT),
            'verification_code' => Str::uuid()->toString(),
            'final_score' => $model->final_score ?? 100,
        ]);
    }
}
