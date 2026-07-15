<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $plans = Plan::all();

        $tenants = [
            [
                'name' => 'Tanzania Institute of Technology',
                'slug' => 'tanzania-institute-of-technology',
                'type' => 'institution',
                'plan_id' => $plans->where('type', 'institution')->where('name', 'Professional Institution')->first()?->id,
                'description' => 'A leading technical institute offering courses in software engineering, data science, and network administration.',
                'contact_email' => 'admin@tit.ac.tz',
                'contact_phone' => '+255 22 123 4567',
                'address' => '123 Education Road, Dar es Salaam, Tanzania',
                'domain' => 'tit.elms.com',
                'status' => 'active',
                'trial_ends_at' => null,
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subMonths(6),
            ],
            [
                'name' => 'Zerixa Learning Academy',
                'slug' => 'zerixa-learning-academy',
                'type' => 'institution',
                'plan_id' => $plans->where('type', 'institution')->where('name', 'Starter Institution')->first()?->id,
                'description' => 'An emerging e-learning academy focused on digital skills, entrepreneurship, and creative arts.',
                'contact_email' => 'info@zerixa.academy',
                'contact_phone' => '+255 78 456 7890',
                'address' => '45 Innovation Hub, Arusha, Tanzania',
                'domain' => 'zerixa.elms.com',
                'status' => 'trialing',
                'trial_ends_at' => now()->addDays(7),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'name' => 'Sarah Kimathi - Independent Instructor',
                'slug' => 'sarah-kimathi-independent',
                'type' => 'solo',
                'plan_id' => $plans->where('type', 'solo')->where('name', 'Solo Teacher Pro')->first()?->id,
                'description' => 'Award-winning instructor specializing in web development, UX design, and digital marketing courses.',
                'contact_email' => 'sarah@kimathi.dev',
                'contact_phone' => '+254 71 234 5678',
                'address' => 'Nairobi, Kenya',
                'domain' => null,
                'status' => 'active',
                'trial_ends_at' => null,
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3),
            ],
            [
                'name' => 'East Africa Business School',
                'slug' => 'east-africa-business-school',
                'type' => 'institution',
                'plan_id' => $plans->where('type', 'institution')->where('name', 'Enterprise Institution')->first()?->id,
                'description' => 'Premier business education provider offering MBA-level courses, leadership training, and professional certifications.',
                'contact_email' => 'admissions@eabs.edu',
                'contact_phone' => '+255 22 987 6543',
                'address' => '78 Business District, Dar es Salaam, Tanzania',
                'domain' => 'eabs.elms.com',
                'status' => 'active',
                'trial_ends_at' => null,
                'created_at' => now()->subYear(),
                'updated_at' => now()->subYear(),
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create($tenant);
        }
    }
}
