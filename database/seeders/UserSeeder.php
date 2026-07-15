<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tit = Tenant::where('slug', 'tanzania-institute-of-technology')->first();
        $zerixa = Tenant::where('slug', 'zerixa-learning-academy')->first();
        $sarah = Tenant::where('slug', 'sarah-kimathi-independent')->first();
        $eabs = Tenant::where('slug', 'east-africa-business-school')->first();

        $users = [
            // Super Admin
            [
                'name' => 'System Administrator',
                'email' => 'admin@elms.com',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'tenant_id' => null,
                'phone' => '+255 22 000 0001',
                'bio' => 'Platform super administrator with full system access.',
                'status' => 'active',
            ],
            // TIT Admin
            [
                'name' => 'Joseph Mwakyusa',
                'email' => 'joseph@tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $tit?->id,
                'phone' => '+255 22 123 4568',
                'bio' => 'Institution administrator at Tanzania Institute of Technology.',
                'status' => 'active',
            ],
            // TIT Teachers
            [
                'name' => 'Dr. Amina Hassan',
                'email' => 'amina@tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'tenant_id' => $tit?->id,
                'phone' => '+255 71 234 5678',
                'bio' => 'PhD in Computer Science. Specializes in algorithms, data structures, and software architecture.',
                'status' => 'active',
            ],
            [
                'name' => 'Prof. David Ochieng',
                'email' => 'david@tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'tenant_id' => $tit?->id,
                'phone' => '+255 72 345 6789',
                'bio' => 'Professor of Data Science with 15 years of experience in machine learning and statistical analysis.',
                'status' => 'active',
            ],
            // TIT Students
            [
                'name' => 'Grace Mushi',
                'email' => 'grace.mushi@student.tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $tit?->id,
                'phone' => '+255 76 456 7890',
                'bio' => 'Second-year software engineering student passionate about mobile app development.',
                'status' => 'active',
            ],
            [
                'name' => 'James Mwita',
                'email' => 'james.mwita@student.tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $tit?->id,
                'phone' => '+255 75 567 8901',
                'bio' => 'Data science enthusiast in his final year. Interested in AI and big data.',
                'status' => 'active',
            ],
            [
                'name' => 'Neema Joseph',
                'email' => 'neema.joseph@student.tit.ac.tz',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $tit?->id,
                'phone' => '+255 78 678 9012',
                'bio' => 'First-year student exploring web development and cybersecurity.',
                'status' => 'active',
            ],
            // Zerixa Admin
            [
                'name' => 'Zawadi Ali',
                'email' => 'zawadi@zerixa.academy',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $zerixa?->id,
                'phone' => '+255 78 456 7891',
                'bio' => 'Founder and administrator of Zerixa Learning Academy.',
                'status' => 'active',
            ],
            // Zerixa Teacher
            [
                'name' => 'Michael Chen',
                'email' => 'michael@zerixa.academy',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'tenant_id' => $zerixa?->id,
                'phone' => '+255 71 456 7892',
                'bio' => 'Creative arts instructor specializing in digital design and content creation.',
                'status' => 'active',
            ],
            // Zerixa Students
            [
                'name' => 'Lucas Anderson',
                'email' => 'lucas@zerixa.academy',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $zerixa?->id,
                'phone' => '+255 76 456 7893',
                'bio' => 'Entrepreneur learning digital marketing and graphic design.',
                'status' => 'active',
            ],
            // Solo Teacher (Sarah Kimathi)
            [
                'name' => 'Sarah Kimathi',
                'email' => 'sarah@kimathi.dev',
                'password' => Hash::make('password123'),
                'role' => 'solo_teacher',
                'tenant_id' => $sarah?->id,
                'phone' => '+254 71 234 5678',
                'bio' => 'Award-winning web developer and instructor. 10+ years building production applications. Featured on major tech conferences across East Africa.',
                'status' => 'active',
            ],
            // EABS Admin
            [
                'name' => 'Robert Nyerere',
                'email' => 'robert@eabs.edu',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $eabs?->id,
                'phone' => '+255 22 987 6544',
                'bio' => 'Dean of digital programs at East Africa Business School.',
                'status' => 'active',
            ],
            // EABS Teachers
            [
                'name' => 'Dr. Esther Wanjiku',
                'email' => 'esther@eabs.edu',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'tenant_id' => $eabs?->id,
                'phone' => '+254 72 345 6780',
                'bio' => 'MBA, PhD Business. Expert in strategic management, leadership, and organizational behavior.',
                'status' => 'active',
            ],
            // EABS Students
            [
                'name' => 'Daniel Kiprop',
                'email' => 'daniel.kiprop@student.eabs.edu',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $eabs?->id,
                'phone' => '+254 73 456 7891',
                'bio' => 'Executive MBA candidate focusing on entrepreneurship and venture capital.',
                'status' => 'active',
            ],
            [
                'name' => 'Aisha Mohammed',
                'email' => 'aisha.mohammed@student.eabs.edu',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'tenant_id' => $eabs?->id,
                'phone' => '+255 74 567 8902',
                'bio' => 'Marketing professional advancing her skills in digital strategy and brand management.',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
