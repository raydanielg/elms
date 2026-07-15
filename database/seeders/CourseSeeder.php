<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\Category;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Review;
use App\Models\Certificate;
use App\Models\Notification;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $tit = Tenant::where('slug', 'tanzania-institute-of-technology')->first();
        $zerixa = Tenant::where('slug', 'zerixa-learning-academy')->first();
        $sarah = Tenant::where('slug', 'sarah-kimathi-independent')->first();
        $eabs = Tenant::where('slug', 'east-africa-business-school')->first();

        $amina = User::where('email', 'amina@tit.ac.tz')->first();
        $david = User::where('email', 'david@tit.ac.tz')->first();
        $michael = User::where('email', 'michael@zerixa.academy')->first();
        $sarahUser = User::where('email', 'sarah@kimathi.dev')->first();
        $esther = User::where('email', 'esther@eabs.edu')->first();

        $swDev = Category::where('name', 'Software Development')->first();
        $dataSci = Category::where('name', 'Data Science')->first();
        $design = Category::where('name', 'Design & Creative')->first();
        $business = Category::where('name', 'Business & Management')->first();
        $marketing = Category::where('name', 'Digital Marketing')->first();

        // ── Course 1: Web Development Fundamentals (TIT - Amina)
        $course1 = Course::create([
            'tenant_id' => $tit->id,
            'owner_id' => $amina->id,
            'category_id' => $swDev->id,
            'title' => 'Web Development Fundamentals',
            'slug' => 'web-development-fundamentals-' . Str::random(6),
            'description' => 'A comprehensive introduction to web development covering HTML5, CSS3, JavaScript ES6+, and responsive design principles. Students will build real-world projects including a personal portfolio and a small business landing page.',
            'level' => 'beginner',
            'status' => 'published',
            'visibility' => 'private',
            'price' => 0,
            'language' => 'English',
            'duration_hours' => 40,
            'has_certificate' => true,
            'drip_enabled' => false,
            'published_at' => now()->subMonths(4),
        ]);

        $mod1 = CourseModule::create(['course_id' => $course1->id, 'title' => 'HTML5 Foundations', 'description' => 'Learn the structure of modern web pages', 'sort_order' => 0]);
        Lesson::create(['module_id' => $mod1->id, 'title' => 'Introduction to HTML', 'content_type' => 'video', 'description' => 'What is HTML and how the web works', 'duration_minutes' => 25, 'is_preview' => true, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $mod1->id, 'title' => 'HTML Document Structure', 'content_type' => 'video', 'description' => 'DOCTYPE, head, body, and semantic tags', 'duration_minutes' => 30, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $mod1->id, 'title' => 'Forms and Input Elements', 'content_type' => 'video', 'description' => 'Building accessible forms', 'duration_minutes' => 35, 'is_published' => true, 'sort_order' => 2]);

        $mod2 = CourseModule::create(['course_id' => $course1->id, 'title' => 'CSS3 and Responsive Design', 'description' => 'Style your pages and make them mobile-friendly', 'sort_order' => 1]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'CSS Selectors and Properties', 'content_type' => 'video', 'duration_minutes' => 28, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'Flexbox Layout', 'content_type' => 'video', 'duration_minutes' => 40, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'CSS Grid System', 'content_type' => 'video', 'duration_minutes' => 38, 'is_published' => true, 'sort_order' => 2]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'Media Queries and Breakpoints', 'content_type' => 'video', 'duration_minutes' => 22, 'is_published' => true, 'sort_order' => 3]);

        $mod3 = CourseModule::create(['course_id' => $course1->id, 'title' => 'JavaScript Essentials', 'description' => 'Add interactivity to your websites', 'sort_order' => 2]);
        Lesson::create(['module_id' => $mod3->id, 'title' => 'Variables and Data Types', 'content_type' => 'video', 'duration_minutes' => 35, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $mod3->id, 'title' => 'Functions and Scope', 'content_type' => 'video', 'duration_minutes' => 42, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $mod3->id, 'title' => 'DOM Manipulation', 'content_type' => 'video', 'duration_minutes' => 45, 'is_published' => true, 'sort_order' => 2]);
        Lesson::create(['module_id' => $mod3->id, 'title' => 'Events and Event Handling', 'content_type' => 'video', 'duration_minutes' => 30, 'is_published' => true, 'sort_order' => 3]);

        // Quiz for course 1
        $quiz1 = Quiz::create([
            'course_id' => $course1->id,
            'title' => 'HTML & CSS Knowledge Check',
            'description' => 'Test your understanding of HTML5 and CSS3 fundamentals',
            'time_limit_minutes' => 30,
            'pass_score' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
            'is_published' => true,
        ]);

        $q1 = Question::create(['quiz_id' => $quiz1->id, 'type' => 'multiple_choice', 'question_text' => 'Which HTML element is used to define the largest heading?', 'points' => 2, 'sort_order' => 0]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '<h6>', 'is_correct' => false, 'sort_order' => 0]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '<heading>', 'is_correct' => false, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '<h1>', 'is_correct' => true, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '<head>', 'is_correct' => false, 'sort_order' => 3]);

        $q2 = Question::create(['quiz_id' => $quiz1->id, 'type' => 'true_false', 'question_text' => 'CSS Grid is a one-dimensional layout system.', 'points' => 1, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'True', 'is_correct' => false, 'sort_order' => 0]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'False', 'is_correct' => true, 'sort_order' => 1]);

        $q3 = Question::create(['quiz_id' => $quiz1->id, 'type' => 'fill_blank', 'question_text' => 'What does CSS stand for?', 'points' => 2, 'correct_answer' => 'Cascading Style Sheets', 'sort_order' => 2]);

        $q4 = Question::create(['quiz_id' => $quiz1->id, 'type' => 'multiple_choice', 'question_text' => 'Which CSS property is used to change the text color?', 'points' => 2, 'sort_order' => 3]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => 'font-color', 'is_correct' => false, 'sort_order' => 0]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => 'text-color', 'is_correct' => false, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => 'color', 'is_correct' => true, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => 'foreground', 'is_correct' => false, 'sort_order' => 3]);

        // Assignment for course 1
        Assignment::create([
            'course_id' => $course1->id,
            'title' => 'Build a Personal Portfolio Page',
            'instructions' => 'Create a single-page personal portfolio using HTML5 and CSS3. Include: a hero section, about me, skills section, projects gallery, and contact form. Use Flexbox or Grid for layout. Submit your HTML and CSS files.',
            'max_points' => 100,
            'due_date' => now()->addWeeks(2),
            'allow_late_submission' => true,
            'late_penalty_percent' => 10,
            'is_published' => true,
        ]);

        // Enrollments
        $grace = User::where('email', 'grace.mushi@student.tit.ac.tz')->first();
        $james = User::where('email', 'james.mwita@student.tit.ac.tz')->first();
        $neema = User::where('email', 'neema.joseph@student.tit.ac.tz')->first();

        $enroll1 = Enrollment::create(['user_id' => $grace->id, 'course_id' => $course1->id, 'status' => 'active', 'progress' => 65, 'last_accessed_at' => now()->subDays(2)]);
        $enroll2 = Enrollment::create(['user_id' => $james->id, 'course_id' => $course1->id, 'status' => 'completed', 'progress' => 100, 'completed_at' => now()->subWeeks(2), 'last_accessed_at' => now()->subWeeks(2)]);
        $enroll3 = Enrollment::create(['user_id' => $neema->id, 'course_id' => $course1->id, 'status' => 'active', 'progress' => 30, 'last_accessed_at' => now()->subDays(5)]);

        // Lesson progress for Grace
        $lessons = $course1->modules->flatMap->lessons;
        foreach ($lessons->take(7) as $lesson) {
            LessonProgress::create(['enrollment_id' => $enroll1->id, 'lesson_id' => $lesson->id, 'status' => 'completed', 'completed_at' => now()->subDays(rand(1, 10))]);
        }

        // Certificate for James
        Certificate::create([
            'user_id' => $james->id,
            'course_id' => $course1->id,
            'certificate_number' => 'ELMS-' . date('Y') . '-00001',
            'verification_code' => Str::uuid()->toString(),
            'final_score' => 95,
        ]);

        // Reviews
        Review::create(['course_id' => $course1->id, 'user_id' => $james->id, 'rating' => 5, 'comment' => 'Excellent course! Dr. Amina explains everything clearly. The projects were challenging but rewarding.']);
        Review::create(['course_id' => $course1->id, 'user_id' => $grace->id, 'rating' => 4, 'comment' => 'Great content and well-structured. Would love more advanced JavaScript topics though.']);

        // ── Course 2: Machine Learning with Python (TIT - David)
        $course2 = Course::create([
            'tenant_id' => $tit->id,
            'owner_id' => $david->id,
            'category_id' => $dataSci->id,
            'title' => 'Machine Learning with Python',
            'slug' => 'machine-learning-with-python-' . Str::random(6),
            'description' => 'Dive into the world of machine learning using Python. Cover supervised and unsupervised learning, model evaluation, and real-world deployment scenarios with scikit-learn and TensorFlow.',
            'level' => 'advanced',
            'status' => 'published',
            'visibility' => 'private',
            'price' => 0,
            'language' => 'English',
            'duration_hours' => 60,
            'has_certificate' => true,
            'published_at' => now()->subMonths(2),
        ]);

        $mlMod1 = CourseModule::create(['course_id' => $course2->id, 'title' => 'Introduction to Machine Learning', 'sort_order' => 0]);
        Lesson::create(['module_id' => $mlMod1->id, 'title' => 'What is Machine Learning?', 'content_type' => 'video', 'duration_minutes' => 35, 'is_preview' => true, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $mlMod1->id, 'title' => 'Types of ML: Supervised vs Unsupervised', 'content_type' => 'video', 'duration_minutes' => 40, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $mlMod1->id, 'title' => 'Setting Up Your Python Environment', 'content_type' => 'video', 'duration_minutes' => 25, 'is_published' => true, 'sort_order' => 2]);

        $mlMod2 = CourseModule::create(['course_id' => $course2->id, 'title' => 'Supervised Learning Algorithms', 'sort_order' => 1]);
        Lesson::create(['module_id' => $mlMod2->id, 'title' => 'Linear Regression', 'content_type' => 'video', 'duration_minutes' => 45, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $mlMod2->id, 'title' => 'Logistic Regression', 'content_type' => 'video', 'duration_minutes' => 42, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $mlMod2->id, 'title' => 'Decision Trees and Random Forests', 'content_type' => 'video', 'duration_minutes' => 50, 'is_published' => true, 'sort_order' => 2]);
        Lesson::create(['module_id' => $mlMod2->id, 'title' => 'Support Vector Machines', 'content_type' => 'video', 'duration_minutes' => 48, 'is_published' => true, 'sort_order' => 3]);

        Enrollment::create(['user_id' => $james->id, 'course_id' => $course2->id, 'status' => 'active', 'progress' => 45, 'last_accessed_at' => now()->subDay()]);

        // ── Course 3: Digital Design Masterclass (Zerixa - Michael)
        $course3 = Course::create([
            'tenant_id' => $zerixa->id,
            'owner_id' => $michael->id,
            'category_id' => $design->id,
            'title' => 'Digital Design Masterclass',
            'slug' => 'digital-design-masterclass-' . Str::random(6),
            'description' => 'Master the principles of digital design from color theory to advanced composition techniques. Learn industry-standard tools and build a professional portfolio.',
            'level' => 'intermediate',
            'status' => 'published',
            'visibility' => 'private',
            'price' => 0,
            'language' => 'English',
            'duration_hours' => 35,
            'has_certificate' => true,
            'published_at' => now()->subMonth(),
        ]);

        $dMod = CourseModule::create(['course_id' => $course3->id, 'title' => 'Design Fundamentals', 'sort_order' => 0]);
        Lesson::create(['module_id' => $dMod->id, 'title' => 'Color Theory Basics', 'content_type' => 'video', 'duration_minutes' => 30, 'is_preview' => true, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $dMod->id, 'title' => 'Typography and Hierarchy', 'content_type' => 'video', 'duration_minutes' => 35, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $dMod->id, 'title' => 'Composition and Layout', 'content_type' => 'video', 'duration_minutes' => 40, 'is_published' => true, 'sort_order' => 2]);

        $lucas = User::where('email', 'lucas@zerixa.academy')->first();
        Enrollment::create(['user_id' => $lucas->id, 'course_id' => $course3->id, 'status' => 'active', 'progress' => 55, 'last_accessed_at' => now()->subDays(3)]);

        // ── Course 4: Modern React Development (Marketplace - Sarah)
        $course4 = Course::create([
            'tenant_id' => $sarah->id,
            'owner_id' => $sarahUser->id,
            'category_id' => $swDev->id,
            'title' => 'Modern React Development: From Zero to Production',
            'slug' => 'modern-react-development-' . Str::random(6),
            'description' => 'Build production-ready React applications from scratch. Learn hooks, context, state management, routing, testing, and deployment. Includes a full-stack project with API integration.',
            'level' => 'intermediate',
            'status' => 'published',
            'visibility' => 'marketplace',
            'price' => 49.99,
            'compare_price' => 99.99,
            'language' => 'English',
            'duration_hours' => 50,
            'has_certificate' => true,
            'published_at' => now()->subMonths(3),
        ]);

        $rMod1 = CourseModule::create(['course_id' => $course4->id, 'title' => 'React Foundations', 'sort_order' => 0]);
        Lesson::create(['module_id' => $rMod1->id, 'title' => 'Why React? Component Thinking', 'content_type' => 'video', 'duration_minutes' => 20, 'is_preview' => true, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $rMod1->id, 'title' => 'JSX and Components', 'content_type' => 'video', 'duration_minutes' => 35, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $rMod1->id, 'title' => 'Props and State', 'content_type' => 'video', 'duration_minutes' => 40, 'is_published' => true, 'sort_order' => 2]);

        $rMod2 = CourseModule::create(['course_id' => $course4->id, 'title' => 'React Hooks Deep Dive', 'sort_order' => 1]);
        Lesson::create(['module_id' => $rMod2->id, 'title' => 'useState and useEffect', 'content_type' => 'video', 'duration_minutes' => 45, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $rMod2->id, 'title' => 'useContext for Global State', 'content_type' => 'video', 'duration_minutes' => 38, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $rMod2->id, 'title' => 'useReducer and Custom Hooks', 'content_type' => 'video', 'duration_minutes' => 42, 'is_published' => true, 'sort_order' => 2]);

        $rMod3 = CourseModule::create(['course_id' => $course4->id, 'title' => 'Building a Full-Stack App', 'sort_order' => 2]);
        Lesson::create(['module_id' => $rMod3->id, 'title' => 'Setting Up the API', 'content_type' => 'video', 'duration_minutes' => 30, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $rMod3->id, 'title' => 'Authentication Flow', 'content_type' => 'video', 'duration_minutes' => 50, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $rMod3->id, 'title' => 'Deployment to Production', 'content_type' => 'video', 'duration_minutes' => 35, 'is_published' => true, 'sort_order' => 2]);

        // Marketplace enrollments
        Enrollment::create(['user_id' => $grace->id, 'course_id' => $course4->id, 'status' => 'active', 'progress' => 20, 'last_accessed_at' => now()->subDays(1)]);
        Enrollment::create(['user_id' => $lucas->id, 'course_id' => $course4->id, 'status' => 'active', 'progress' => 80, 'last_accessed_at' => now()->subDays(4)]);

        Review::create(['course_id' => $course4->id, 'user_id' => $lucas->id, 'rating' => 5, 'comment' => 'Sarah is an amazing instructor! The course is well-paced and the projects are real-world applicable. Worth every penny.']);
        Review::create(['course_id' => $course4->id, 'user_id' => $grace->id, 'rating' => 5, 'comment' => 'Best React course I have taken. The hooks section alone is worth the price.']);

        // ── Course 5: Strategic Leadership (EABS - Esther)
        $course5 = Course::create([
            'tenant_id' => $eabs->id,
            'owner_id' => $esther->id,
            'category_id' => $business->id,
            'title' => 'Strategic Leadership in the Digital Age',
            'slug' => 'strategic-leadership-digital-age-' . Str::random(6),
            'description' => 'Develop the leadership skills needed to navigate organizations through digital transformation. Learn strategic planning, change management, and how to build high-performing teams.',
            'level' => 'intermediate',
            'status' => 'published',
            'visibility' => 'private',
            'price' => 0,
            'language' => 'English',
            'duration_hours' => 30,
            'has_certificate' => true,
            'published_at' => now()->subMonths(5),
        ]);

        $lMod = CourseModule::create(['course_id' => $course5->id, 'title' => 'Foundations of Strategic Leadership', 'sort_order' => 0]);
        Lesson::create(['module_id' => $lMod->id, 'title' => 'What Makes a Great Leader?', 'content_type' => 'video', 'duration_minutes' => 30, 'is_preview' => true, 'is_published' => true, 'sort_order' => 0]);
        Lesson::create(['module_id' => $lMod->id, 'title' => 'Strategic Planning Frameworks', 'content_type' => 'video', 'duration_minutes' => 45, 'is_published' => true, 'sort_order' => 1]);
        Lesson::create(['module_id' => $lMod->id, 'title' => 'Leading Through Change', 'content_type' => 'video', 'duration_minutes' => 40, 'is_published' => true, 'sort_order' => 2]);

        $daniel = User::where('email', 'daniel.kiprop@student.eabs.edu')->first();
        $aisha = User::where('email', 'aisha.mohammed@student.eabs.edu')->first();

        $enrollD = Enrollment::create(['user_id' => $daniel->id, 'course_id' => $course5->id, 'status' => 'completed', 'progress' => 100, 'completed_at' => now()->subMonth(), 'last_accessed_at' => now()->subMonth()]);
        Enrollment::create(['user_id' => $aisha->id, 'course_id' => $course5->id, 'status' => 'active', 'progress' => 50, 'last_accessed_at' => now()->subDays(3)]);

        Certificate::create([
            'user_id' => $daniel->id,
            'course_id' => $course5->id,
            'certificate_number' => 'ELMS-' . date('Y') . '-00002',
            'verification_code' => Str::uuid()->toString(),
            'final_score' => 88,
        ]);

        Review::create(['course_id' => $course5->id, 'user_id' => $daniel->id, 'rating' => 5, 'comment' => 'Transformative course. Dr. Esther brings real-world examples that made the concepts stick. Highly recommended for any aspiring leader.']);

        // ── Notifications
        $notifications = [
            ['user_id' => $grace->id, 'type' => 'success', 'title' => 'Lesson Completed', 'body' => 'You completed "CSS Selectors and Properties". Keep going!', 'read_at' => null],
            ['user_id' => $grace->id, 'type' => 'info', 'title' => 'New Course Available', 'body' => 'Modern React Development is now available on the marketplace.', 'read_at' => null],
            ['user_id' => $james->id, 'type' => 'success', 'title' => 'Certificate Earned', 'body' => 'Congratulations! You earned a certificate for Web Development Fundamentals.', 'read_at' => now()],
            ['user_id' => $james->id, 'type' => 'info', 'title' => 'New Lesson Available', 'body' => 'A new lesson "Decision Trees and Random Forests" is now available.', 'read_at' => null],
            ['user_id' => $amina->id, 'type' => 'info', 'title' => 'New Enrollment', 'body' => 'Neema Joseph enrolled in Web Development Fundamentals.', 'read_at' => null],
            ['user_id' => $daniel->id, 'type' => 'success', 'title' => 'Certificate Earned', 'body' => 'You earned a certificate for Strategic Leadership in the Digital Age.', 'read_at' => now()],
            ['user_id' => $sarahUser->id, 'type' => 'success', 'title' => 'New Sale!', 'body' => 'Lucas Anderson purchased Modern React Development. You earned $44.99.', 'read_at' => null],
            ['user_id' => $lucas->id, 'type' => 'info', 'title' => 'Assignment Due Soon', 'body' => 'Your portfolio assignment is due in 2 weeks.', 'read_at' => null],
        ];

        foreach ($notifications as $n) {
            Notification::create(array_merge($n, ['created_at' => now()->subDays(rand(1, 7)), 'updated_at' => now()->subDays(rand(1, 7))]));
        }

        // ── Announcements
        Announcement::create([
            'tenant_id' => $tit->id,
            'user_id' => $amina->id,
            'title' => 'New Machine Learning Course Now Available',
            'body' => 'We are excited to announce that "Machine Learning with Python" is now open for enrollment. This advanced course covers supervised and unsupervised learning, model evaluation, and deployment. Seats are limited!',
            'audience' => 'all',
            'is_pinned' => true,
        ]);

        Announcement::create([
            'tenant_id' => $tit->id,
            'user_id' => User::where('email', 'joseph@tit.ac.tz')->first()->id,
            'title' => 'Scheduled Maintenance This Weekend',
            'body' => 'The platform will undergo scheduled maintenance on Saturday from 2 AM to 4 AM. During this time, the system will be unavailable. Please plan accordingly.',
            'audience' => 'all',
            'is_pinned' => false,
        ]);

        Announcement::create([
            'tenant_id' => $eabs->id,
            'user_id' => $esther->id,
            'title' => 'Welcome to Strategic Leadership',
            'body' => 'Welcome to all new students! I am thrilled to have you in this course. Our first module covers the foundations of strategic leadership. Please introduce yourself in the discussion forum.',
            'audience' => 'students',
            'course_id' => $course5->id,
            'is_pinned' => true,
        ]);

        // ── Assignment Submission
        AssignmentSubmission::create([
            'assignment_id' => Assignment::where('course_id', $course1->id)->first()->id,
            'user_id' => $james->id,
            'submission_text' => 'I built my portfolio using semantic HTML5 and CSS Grid for the layout. The hero section uses a gradient background with my name and tagline. I included sections for About, Skills, Projects, and Contact. The contact form has proper validation attributes.',
            'status' => 'graded',
            'score' => 92,
            'feedback' => 'Excellent work! Your use of semantic HTML and CSS Grid shows great understanding. The form validation is well done. Minor improvement: consider adding responsive breakpoints for mobile devices.',
            'graded_at' => now()->subWeek(),
            'graded_by' => $amina->id,
        ]);
    }
}
