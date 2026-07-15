<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Badge;
use App\Models\BadgeRule;
use App\Models\StudentBadge;
use App\Models\PointsLedger;
use App\Models\Level;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RecognitionEngine
{
    public function handleEvent(string $event, $model): void
    {
        $this->checkBadgeRules($event, $model);
        $this->checkCertificateRules($event, $model);
    }

    protected function checkBadgeRules(string $event, $model): void
    {
        $tenantId = $this->getTenantId($model);
        $rules = BadgeRule::where('trigger_event', $event)->where('is_active', true)
            ->whereHas('badge', fn($q) => $q->where('is_active', true)
                ->where(function ($q2) use ($tenantId) {
                    $q2->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
                }))
            ->get();

        foreach ($rules as $rule) {
            if (!$rule->passesConditions($model)) continue;

            $userId = $this->getUserId($model);
            if (!$userId) continue;

            $this->awardBadge($rule->badge_id, $userId, $model);
        }
    }

    protected function checkCertificateRules(string $event, $model): void
    {
        if ($event !== 'enrollment.completed') return;
        if (!($model instanceof Enrollment) || $model->status !== 'completed') return;

        $this->issueCertificate($model);
    }

    public function issueCertificate(Enrollment $enrollment, string $type = 'course_completion', ?int $issuedBy = null): ?Certificate
    {
        $existing = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->where('status', 'valid')
            ->first();

        if ($existing) return $existing;

        $course = $enrollment->course;
        $tenantId = $course->tenant_id;
        $template = CertificateTemplate::forTenant($tenantId)->where('type', $type)->first();

        $cert = Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'tenant_id' => $tenantId,
            'template_id' => $template?->id,
            'type' => $type,
            'status' => 'valid',
            'title' => $type === 'course_completion' ? "Certificate of Completion — {$course->title}" : "Certificate — {$course->title}",
            'certificate_number' => 'ELMS-' . date('Y') . '-' . str_pad(Certificate::count() + 1, 5, '0', STR_PAD_LEFT),
            'verification_code' => Str::uuid()->toString(),
            'final_score' => $enrollment->final_score ?? 100,
            'issued_by' => $issuedBy,
            'issued_at' => now(),
            'metadata' => [
                'course_title' => $course->title,
                'student_name' => $enrollment->user->name,
                'tenant_name' => $course->tenant?->name,
            ],
        ]);

        $cert->update(['data_hash' => $cert->generateDataHash()]);

        app(NotificationService::class)->notifyEvent('certificate.issued', [
            'user_id' => $enrollment->user_id,
            'student_name' => $enrollment->user->name,
            'course_title' => $course->title,
            'code' => $cert->verification_code,
            'email' => $enrollment->user->email,
            'phone' => $enrollment->user->phone,
        ], $tenantId);

        return $cert;
    }

    public function awardBadge(int $badgeId, int $userId, $contextModel = null): ?StudentBadge
    {
        $existing = StudentBadge::where('user_id', $userId)->where('badge_id', $badgeId)->first();
        if ($existing) return null;

        $badge = Badge::find($badgeId);
        if (!$badge) return null;

        $studentBadge = StudentBadge::create([
            'user_id' => $userId,
            'badge_id' => $badgeId,
            'course_id' => $contextModel && method_exists($contextModel, 'course') ? $contextModel->course?->id : null,
            'metadata' => ['awarded_at' => now()->toIso8601String()],
        ]);

        if ($badge->xp_reward > 0) {
            PointsLedger::award($userId, 'badge_earned', $badge->xp_reward, "Badge: {$badge->name}", $badge);
        }

        app(NotificationService::class)->notify(
            $userId,
            'success',
            'Badge Earned!',
            "You earned the \"{$badge->name}\" badge. {$badge->description}"
        );

        return $studentBadge;
    }

    public function revokeCertificate(Certificate $cert, string $reason): void
    {
        $cert->revoke($reason);
    }

    protected function getTenantId($model): ?int
    {
        if ($model instanceof Enrollment) return $model->course?->tenant_id;
        if (method_exists($model, 'tenant')) return $model->tenant?->id;
        if (isset($model->tenant_id)) return $model->tenant_id;
        return auth()->user()?->tenant_id;
    }

    protected function getUserId($model): ?int
    {
        if ($model instanceof Enrollment) return $model->user_id;
        if (isset($model->user_id)) return $model->user_id;
        if ($model instanceof User) return $model->id;
        return null;
    }
}
