<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Institution',
                'slug' => 'starter-institution',
                'type' => 'institution',
                'description' => 'Perfect for small schools getting started with online learning. Includes core LMS features with basic limits.',
                'price_monthly' => 29.00,
                'price_yearly' => 290.00,
                'max_teachers' => 5,
                'max_students' => 100,
                'max_courses' => 20,
                'storage_limit_gb' => 10,
                'commission_rate' => 0,
                'features' => json_encode(['core_lms', 'quizzes', 'assignments', 'certificates', 'email_support']),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Institution',
                'slug' => 'professional-institution',
                'type' => 'institution',
                'description' => 'For growing institutions that need more capacity, advanced analytics, and priority support.',
                'price_monthly' => 99.00,
                'price_yearly' => 990.00,
                'max_teachers' => 25,
                'max_students' => 500,
                'max_courses' => 100,
                'storage_limit_gb' => 50,
                'commission_rate' => 0,
                'features' => json_encode(['core_lms', 'quizzes', 'assignments', 'certificates', 'advanced_analytics', 'priority_support', 'custom_branding', 'video_processing']),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise Institution',
                'slug' => 'enterprise-institution',
                'type' => 'institution',
                'description' => 'Unlimited everything for large universities and corporate training programs. White-label and API access included.',
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00,
                'max_teachers' => 0,
                'max_students' => 0,
                'max_courses' => 0,
                'storage_limit_gb' => 500,
                'commission_rate' => 0,
                'features' => json_encode(['core_lms', 'quizzes', 'assignments', 'certificates', 'advanced_analytics', 'priority_support', 'custom_branding', 'video_processing', 'api_access', 'white_label', 'sso', 'dedicated_manager']),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Solo Teacher Free',
                'slug' => 'solo-teacher-free',
                'type' => 'solo',
                'description' => 'Get started as an independent instructor. Publish up to 3 courses on the marketplace with no upfront cost.',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'max_teachers' => 1,
                'max_students' => 50,
                'max_courses' => 3,
                'storage_limit_gb' => 2,
                'commission_rate' => 20.00,
                'features' => json_encode(['core_lms', 'quizzes', 'marketplace_listing', 'basic_analytics']),
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Solo Teacher Pro',
                'slug' => 'solo-teacher-pro',
                'type' => 'solo',
                'description' => 'Unlock unlimited courses, lower commission rates, and advanced marketing tools for serious creators.',
                'price_monthly' => 19.00,
                'price_yearly' => 190.00,
                'max_teachers' => 1,
                'max_students' => 0,
                'max_courses' => 0,
                'storage_limit_gb' => 20,
                'commission_rate' => 10.00,
                'features' => json_encode(['core_lms', 'quizzes', 'assignments', 'marketplace_listing', 'advanced_analytics', 'coupons', 'affiliate_tools', 'priority_support']),
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plans')->insert($plans);
    }
}
