<?php

namespace Database\Seeders;

use App\Models\InstructorLevel;
use App\Models\InstructorLevelHistory;
use App\Models\RevenueShare;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstructorMonetizationSeeder extends Seeder
{
    public function run(): void
    {
        // Instructor Levels
        $levels = [
            ['level_number' => 1, 'name' => 'Starter', 'slug' => 'starter', 'min_sales' => 0, 'min_rating' => 0, 'max_refund_rate' => 100, 'commission_rate' => 25, 'payout_speed_days' => 7, 'badge_icon' => '🌱', 'badge_color' => '#10B981', 'perks' => ['Basic marketplace listing']],
            ['level_number' => 2, 'name' => 'Rising', 'slug' => 'rising', 'min_sales' => 11, 'min_rating' => 4.0, 'max_refund_rate' => 15, 'commission_rate' => 20, 'payout_speed_days' => 5, 'badge_icon' => '📈', 'badge_color' => '#3B82F6', 'perks' => ['Improved search ranking']],
            ['level_number' => 3, 'name' => 'Established', 'slug' => 'established', 'min_sales' => 51, 'min_rating' => 4.3, 'max_refund_rate' => 10, 'commission_rate' => 15, 'payout_speed_days' => 3, 'badge_icon' => '🏆', 'badge_color' => '#F6891F', 'perks' => ['Rising Instructor badge', 'Featured rotation eligibility']],
            ['level_number' => 4, 'name' => 'Pro', 'slug' => 'pro', 'min_sales' => 201, 'min_rating' => 4.5, 'max_refund_rate' => 8, 'commission_rate' => 10, 'payout_speed_days' => 2, 'badge_icon' => '⭐', 'badge_color' => '#8B5CF6', 'perks' => ['Priority support', 'Early access to new features']],
            ['level_number' => 5, 'name' => 'Elite', 'slug' => 'elite', 'min_sales' => 1000, 'min_rating' => 4.7, 'max_refund_rate' => 5, 'commission_rate' => 7, 'payout_speed_days' => 1, 'badge_icon' => '👑', 'badge_color' => '#5A0917', 'perks' => ['Verified Elite Instructor badge', 'Homepage featured placement', 'Priority payout processing']],
        ];

        $levelIds = [];
        foreach ($levels as $level) {
            $l = InstructorLevel::create(array_merge($level, ['is_active' => true, 'sort_order' => $level['level_number']]));
            $levelIds[$level['name']] = $l->id;
        }

        // Assign Starter level to all existing teachers/solo_teachers
        $instructors = User::whereIn('role', ['teacher', 'solo_teacher'])->get();
        foreach ($instructors as $instructor) {
            $instructor->update(['instructor_level_id' => $levelIds['Starter']]);
            InstructorLevelHistory::create([
                'user_id' => $instructor->id,
                'instructor_level_id' => $levelIds['Starter'],
                'reason' => 'initial_assignment',
                'is_manual_override' => false,
            ]);

            // Ensure wallet exists
            Wallet::getOrCreateForUser($instructor->id);
        }

        // Create revenue shares for institution teachers
        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $tenant) {
            $teachers = User::where('tenant_id', $tenant->id)->where('role', 'teacher')->get();
            foreach ($teachers as $teacher) {
                RevenueShare::create([
                    'tenant_id' => $tenant->id,
                    'teacher_id' => $teacher->id,
                    'institution_percentage' => 40,
                    'teacher_percentage' => 60,
                    'is_active' => true,
                ]);
            }
        }

        // Update existing courses with pricing_model
        Course::whereNull('pricing_model')->orWhere('pricing_model', '')->update(['pricing_model' => 'one_time']);
    }
}
