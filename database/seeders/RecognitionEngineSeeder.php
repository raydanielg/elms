<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use App\Models\Badge;
use App\Models\BadgeRule;
use App\Models\Level;
use App\Models\PointsLedger;
use App\Models\StudentBadge;
use App\Models\Award;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecognitionEngineSeeder extends Seeder
{
    public function run(): void
    {
        // Certificate Templates (global + per tenant)
        $layouts = ['classic', 'modern', 'minimal', 'institutional'];
        foreach ($layouts as $layout) {
            CertificateTemplate::create([
                'name' => ucfirst($layout) . ' Template',
                'type' => 'course_completion',
                'layout' => $layout,
                'font_family' => $layout === 'modern' ? 'Inter' : ($layout === 'minimal' ? 'Poppins' : 'Georgia'),
                'primary_color' => '#5A0917',
                'secondary_color' => '#F6891F',
                'show_grade' => true,
                'show_qr_code' => true,
                'show_signature' => true,
                'show_logo' => true,
                'is_active' => true,
            ]);
        }

        // Per-tenant templates
        foreach (Tenant::all() as $tenant) {
            CertificateTemplate::create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name . ' — Default',
                'type' => 'course_completion',
                'layout' => 'institutional',
                'primary_color' => '#5A0917',
                'secondary_color' => '#F6891F',
                'is_active' => true,
            ]);
        }

        // Badges (global)
        $badges = [
            ['name' => 'First Course Completed', 'slug' => 'first-course', 'description' => 'Completed your first course', 'icon' => '🎓', 'category' => 'milestone', 'color' => '#F6891F', 'xp_reward' => 50],
            ['name' => 'Dedicated Learner', 'slug' => 'dedicated-learner', 'description' => 'Completed 5 courses', 'icon' => '📚', 'category' => 'milestone', 'color' => '#3B82F6', 'xp_reward' => 100],
            ['name' => 'Quiz Master', 'slug' => 'quiz-master', 'description' => 'Passed 10 quizzes', 'icon' => '🧠', 'category' => 'skill', 'color' => '#8B5CF6', 'xp_reward' => 75],
            ['name' => 'Perfectionist', 'slug' => 'perfectionist', 'description' => 'Scored 100% on a quiz first attempt', 'icon' => '💯', 'category' => 'skill', 'color' => '#10B981', 'xp_reward' => 50],
            ['name' => '7-Day Streak', 'slug' => 'streak-7', 'description' => 'Logged in 7 days in a row', 'icon' => '🔥', 'category' => 'engagement', 'color' => '#EF4444', 'xp_reward' => 30],
            ['name' => '30-Day Streak', 'slug' => 'streak-30', 'description' => 'Logged in 30 days in a row', 'icon' => '⚡', 'category' => 'engagement', 'color' => '#F59E0B', 'xp_reward' => 100],
            ['name' => 'Early Bird', 'slug' => 'early-bird', 'description' => 'Completed a lesson within 24 hours of unlock', 'icon' => '🐦', 'category' => 'engagement', 'color' => '#06B6D4', 'xp_reward' => 20],
            ['name' => 'Helpful Contributor', 'slug' => 'helpful', 'description' => 'Answered 10+ forum questions', 'icon' => '🤝', 'category' => 'community', 'color' => '#EC4899', 'xp_reward' => 40],
            ['name' => 'First Reply', 'slug' => 'first-reply', 'description' => 'Posted your first forum reply', 'icon' => '💬', 'category' => 'community', 'color' => '#6366F1', 'xp_reward' => 10],
            ['name' => '50 Hours of Learning', 'slug' => '50-hours', 'description' => 'Completed 50 hours of learning content', 'icon' => '⏰', 'category' => 'milestone', 'color' => '#14B8A6', 'xp_reward' => 80],
        ];

        $badgeIds = [];
        foreach ($badges as $badge) {
            $b = Badge::create(array_merge($badge, ['is_active' => true]));
            $badgeIds[$badge['slug']] = $b->id;
        }

        // Badge Rules
        $rules = [
            ['badge_id' => $badgeIds['first-course'], 'trigger_event' => 'enrollment.completed', 'conditions' => null],
            ['badge_id' => $badgeIds['perfectionist'], 'trigger_event' => 'quiz.submitted', 'conditions' => ['score' => 100, 'is_first_attempt' => true]],
            ['badge_id' => $badgeIds['streak-7'], 'trigger_event' => 'login.streak', 'conditions' => ['streak_days' => 7]],
            ['badge_id' => $badgeIds['streak-30'], 'trigger_event' => 'login.streak', 'conditions' => ['streak_days' => 30]],
            ['badge_id' => $badgeIds['early-bird'], 'trigger_event' => 'lesson.completed', 'conditions' => ['within_hours' => 24]],
            ['badge_id' => $badgeIds['first-reply'], 'trigger_event' => 'forum.reply_created', 'conditions' => null],
        ];

        foreach ($rules as $rule) {
            BadgeRule::create(array_merge($rule, ['is_active' => true]));
        }

        // Levels
        $levels = [
            ['level_number' => 1, 'name' => 'Beginner', 'min_xp' => 0, 'max_xp' => 100, 'icon' => '🌱'],
            ['level_number' => 2, 'name' => 'Learner', 'min_xp' => 101, 'max_xp' => 300, 'icon' => '📖'],
            ['level_number' => 3, 'name' => 'Explorer', 'min_xp' => 301, 'max_xp' => 600, 'icon' => '🧭'],
            ['level_number' => 4, 'name' => 'Achiever', 'min_xp' => 601, 'max_xp' => 1000, 'icon' => '🎯'],
            ['level_number' => 5, 'name' => 'Scholar', 'min_xp' => 1001, 'max_xp' => 1500, 'icon' => '🎓'],
            ['level_number' => 6, 'name' => 'Expert', 'min_xp' => 1501, 'max_xp' => 2500, 'icon' => '🏆'],
            ['level_number' => 7, 'name' => 'Master', 'min_xp' => 2501, 'max_xp' => 4000, 'icon' => '👑'],
            ['level_number' => 8, 'name' => 'Legend', 'min_xp' => 4001, 'max_xp' => 999999, 'icon' => '⭐'],
        ];

        foreach ($levels as $level) {
            Level::create(array_merge($level, ['perks' => null]));
        }

        // Award some badges to students who have completed courses
        $completedEnrollments = Enrollment::where('status', 'completed')->get();
        $firstCourseBadgeId = $badgeIds['first-course'] ?? null;

        if ($firstCourseBadgeId) {
            $awardedUsers = [];
            foreach ($completedEnrollments as $enrollment) {
                if (in_array($enrollment->user_id, $awardedUsers)) continue;
                StudentBadge::create([
                    'user_id' => $enrollment->user_id,
                    'badge_id' => $firstCourseBadgeId,
                    'course_id' => $enrollment->course_id,
                    'metadata' => ['awarded_at' => now()->toIso8601String()],
                ]);
                $awardedUsers[] = $enrollment->user_id;
            }
        }

        // Award XP points for completed enrollments
        foreach ($completedEnrollments as $enrollment) {
            PointsLedger::award($enrollment->user_id, 'course_completed', 100, "Completed: {$enrollment->course->title}", $enrollment);
        }

        // Create some awards
        $students = User::where('role', 'student')->limit(3)->get();
        foreach ($students as $index => $student) {
            Award::create([
                'tenant_id' => $student->tenant_id,
                'title' => 'Student of the Month',
                'description' => 'Outstanding performance and dedication',
                'type' => 'student_of_month',
                'awarded_to' => $student->id,
                'awarded_by' => User::where('role', 'super_admin')->first()?->id,
                'period' => now()->format('F Y'),
                'is_public' => true,
            ]);
        }

        // Update existing certificates with new fields
        $existingCerts = Certificate::all();
        foreach ($existingCerts as $cert) {
            if (!$cert->data_hash) {
                $cert->update([
                    'data_hash' => $cert->generateDataHash(),
                    'status' => 'valid',
                    'type' => 'course_completion',
                    'tenant_id' => $cert->course?->tenant_id,
                ]);
            }
        }
    }
}
