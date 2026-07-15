<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Software Development', 'icon' => 'code', 'description' => 'Programming, web development, mobile apps, and software engineering courses.'],
            ['name' => 'Data Science', 'icon' => 'chart', 'description' => 'Machine learning, statistics, data visualization, and big data analytics.'],
            ['name' => 'Business & Management', 'icon' => 'briefcase', 'description' => 'Leadership, entrepreneurship, project management, and business strategy.'],
            ['name' => 'Digital Marketing', 'icon' => 'megaphone', 'description' => 'SEO, social media marketing, content strategy, and paid advertising.'],
            ['name' => 'Design & Creative', 'icon' => 'palette', 'description' => 'UI/UX design, graphic design, video editing, and digital art.'],
            ['name' => 'Cybersecurity', 'icon' => 'shield', 'description' => 'Network security, ethical hacking, compliance, and threat analysis.'],
            ['name' => 'Finance & Accounting', 'icon' => 'dollar', 'description' => 'Financial analysis, accounting principles, investment, and fintech.'],
            ['name' => 'Languages', 'icon' => 'globe', 'description' => 'English, Swahili, French, and other language learning courses.'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']) . '-' . Str::random(6),
                'icon' => $cat['icon'],
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }
    }
}
