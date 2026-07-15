<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use App\Models\MenuItem;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\SmsTemplate;
use App\Models\SmsBundle;
use App\Models\Currency;
use App\Models\NotificationTemplate;
use App\Models\NotificationTrigger;
use App\Models\TenantTheme;
use Illuminate\Database\Seeder;

class AdvancedFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Feature Flags
        $flags = [
            ['key' => 'live_classes', 'label' => 'Live Classes', 'description' => 'Enable/disable live class sessions'],
            ['key' => 'forums', 'label' => 'Discussion Forums', 'description' => 'Course-level discussion boards'],
            ['key' => 'gamification', 'label' => 'Gamification', 'description' => 'Points, badges, and leaderboards'],
            ['key' => 'certificates', 'label' => 'Certificates', 'description' => 'Auto-generated course certificates'],
            ['key' => 'marketplace', 'label' => 'Marketplace', 'description' => 'Public course marketplace for solo teachers'],
            ['key' => 'affiliate_program', 'label' => 'Affiliate Program', 'description' => 'Referral links and commission tracking'],
            ['key' => 'ai_features', 'label' => 'AI Features', 'description' => 'AI quiz generator, content summarizer, chat assistant'],
            ['key' => 'proctoring', 'label' => 'Exam Proctoring', 'description' => 'Webcam monitoring for high-stakes quizzes'],
            ['key' => 'custom_fields', 'label' => 'Custom Fields', 'description' => 'Dynamic forms with custom fields'],
            ['key' => 'automation_rules', 'label' => 'Automation Rules', 'description' => 'If-this-then-that workflow engine'],
            ['key' => 'webhooks', 'label' => 'Outgoing Webhooks', 'description' => 'Send event data to external systems'],
            ['key' => 'api_access', 'label' => 'Developer API', 'description' => 'API key generation for integrations'],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::create(array_merge($flag, [
                'is_global_enabled' => true,
                'plan_ids' => null,
                'tenant_overrides' => null,
            ]));
        }

        // Menu Items
        $menus = [
            ['label' => 'Dashboard', 'route' => 'home', 'icon' => 'home', 'roles' => ['super_admin', 'admin', 'teacher', 'solo_teacher', 'student'], 'sort_order' => 0],
            ['label' => 'Courses', 'route' => 'courses.index', 'icon' => 'book', 'roles' => ['super_admin', 'admin', 'teacher', 'solo_teacher', 'student'], 'sort_order' => 1],
            ['label' => 'Marketplace', 'route' => 'marketplace.index', 'icon' => 'shopping-bag', 'roles' => ['super_admin', 'admin', 'teacher', 'solo_teacher', 'student'], 'sort_order' => 2],
            ['label' => 'My Wishlist', 'route' => 'wishlist.index', 'icon' => 'heart', 'roles' => ['student'], 'sort_order' => 3],
            ['label' => 'Enrollments', 'route' => 'enrollments.index', 'icon' => 'clipboard-check', 'roles' => ['student'], 'sort_order' => 4],
            ['label' => 'Certificates', 'route' => 'certificates.index', 'icon' => 'award', 'roles' => ['student'], 'sort_order' => 5],
            ['label' => 'Referrals', 'route' => 'referrals.index', 'icon' => 'share', 'roles' => ['solo_teacher', 'student'], 'sort_order' => 6],
            ['label' => 'Wallet', 'route' => 'wallet.index', 'icon' => 'wallet', 'roles' => ['solo_teacher'], 'sort_order' => 7],
            ['label' => 'Announcements', 'route' => 'announcements.index', 'icon' => 'megaphone', 'roles' => ['super_admin', 'admin', 'teacher', 'solo_teacher', 'student'], 'sort_order' => 8],
            ['label' => 'Users', 'route' => 'users.index', 'icon' => 'users', 'roles' => ['super_admin', 'admin'], 'sort_order' => 9],
            ['label' => 'Tenants', 'route' => 'tenants.index', 'icon' => 'building', 'roles' => ['super_admin'], 'sort_order' => 10],
            ['label' => 'Plans', 'route' => 'plans.index', 'icon' => 'credit-card', 'roles' => ['super_admin'], 'sort_order' => 11],
            ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog', 'roles' => ['super_admin', 'admin'], 'sort_order' => 12],
        ];

        foreach ($menus as $menu) {
            MenuItem::create(array_merge($menu, ['is_visible' => true]));
        }

        // Payment Gateways
        PaymentGateway::create([
            'driver' => 'stripe',
            'label' => 'Stripe',
            'category' => 'card',
            'is_active' => true,
            'priority' => 1,
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'KES', 'TZS'],
            'supported_countries' => ['US', 'GB', 'KE', 'TZ'],
        ]);

        PaymentGateway::create([
            'driver' => 'paypal',
            'label' => 'PayPal',
            'category' => 'card',
            'is_active' => true,
            'priority' => 2,
            'supported_currencies' => ['USD', 'EUR', 'GBP'],
        ]);

        PaymentGateway::create([
            'driver' => 'mpesa',
            'label' => 'M-Pesa',
            'category' => 'mobile_money',
            'is_active' => true,
            'priority' => 3,
            'supported_currencies' => ['KES', 'TZS'],
            'supported_countries' => ['KE', 'TZ'],
        ]);

        PaymentGateway::create([
            'driver' => 'flutterwave',
            'label' => 'Flutterwave',
            'category' => 'aggregator',
            'is_active' => true,
            'priority' => 4,
            'supported_currencies' => ['USD', 'KES', 'TZS', 'UGX', 'NGN'],
        ]);

        // SMS Gateways
        SmsGateway::create([
            'driver' => 'africas_talking',
            'label' => "Africa's Talking",
            'is_active' => true,
            'priority' => 1,
            'sender_id' => 'ELMS',
        ]);

        SmsGateway::create([
            'driver' => 'beem_africa',
            'label' => 'Beem Africa',
            'is_active' => false,
            'priority' => 2,
            'sender_id' => 'ELMS',
        ]);

        // SMS Templates
        $smsTemplates = [
            ['key' => 'payment_confirmation', 'event' => 'payment.succeeded', 'category' => 'critical', 'template' => 'Hello {{student_name}}, your payment of {{amount}} for {{course_title}} has been received. Thank you!'],
            ['key' => 'quiz_result_published', 'event' => 'quiz.submitted', 'category' => 'critical', 'template' => 'Hi {{student_name}}, your result for {{quiz_title}} is now available. Score: {{score}}%'],
            ['key' => 'assignment_deadline_reminder', 'event' => 'assignment.due_soon', 'category' => 'critical', 'template' => 'Reminder: Your assignment "{{assignment_title}}" for {{course_title}} is due tomorrow.'],
            ['key' => 'live_class_starting', 'event' => 'live_class.starting', 'category' => 'critical', 'template' => 'Your live class "{{class_title}}" starts in 15 minutes. Join now!'],
            ['key' => 'subscription_expiring', 'event' => 'subscription.expiring', 'category' => 'critical', 'template' => 'Your institution plan expires in {{days}} days. Please renew to avoid service interruption.'],
            ['key' => 'payout_processed', 'event' => 'payout.completed', 'category' => 'critical', 'template' => 'Your withdrawal of {{amount}} has been sent. Expected delivery: 1-2 business days.'],
            ['key' => 'new_enrollment_teacher', 'event' => 'enrollment.created', 'category' => 'non_critical', 'template' => 'Good news! {{student_name}} just enrolled in your course "{{course_title}}".'],
            ['key' => 'reengagement_reminder', 'event' => 'user.inactive', 'category' => 'non_critical', 'template' => 'Hi {{student_name}}, we miss you! Come back and continue your learning journey on ELMS.'],
            ['key' => 'otp_verification', 'event' => 'user.registered', 'category' => 'critical', 'template' => 'Your ELMS verification code is: {{code}}. Valid for 10 minutes.'],
            ['key' => 'password_reset', 'event' => 'password.reset', 'category' => 'critical', 'template' => 'Your ELMS password reset code is: {{code}}. If you did not request this, ignore this message.'],
        ];

        foreach ($smsTemplates as $tpl) {
            SmsTemplate::create(array_merge($tpl, ['language' => 'en', 'is_active' => true]));
        }

        // SMS Bundles
        $bundles = [
            ['name' => 'Starter', 'credits' => 100, 'price' => 5.00],
            ['name' => 'Standard', 'credits' => 500, 'price' => 20.00],
            ['name' => 'Professional', 'credits' => 2000, 'price' => 70.00],
            ['name' => 'Enterprise', 'credits' => 5000, 'price' => 150.00],
        ];

        foreach ($bundles as $bundle) {
            SmsBundle::create(array_merge($bundle, ['is_active' => true]));
        }

        // Currencies
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'locale' => 'en_US', 'exchange_rate' => 1.000000],
            ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'locale' => 'sw_TZ', 'exchange_rate' => 2535.000000],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'locale' => 'sw_KE', 'exchange_rate' => 129.000000],
            ['code' => 'UGX', 'name' => 'Ugandan Shilling', 'symbol' => 'USh', 'locale' => 'en_UG', 'exchange_rate' => 3750.000000],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'en_EU', 'exchange_rate' => 0.920000],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'locale' => 'en_GB', 'exchange_rate' => 0.790000],
        ];

        foreach ($currencies as $currency) {
            Currency::create(array_merge($currency, ['is_active' => true]));
        }

        // Notification Templates
        $notifTemplates = [
            ['key' => 'enrollment_created_email', 'event' => 'enrollment.created', 'channel' => 'email', 'subject' => 'Welcome to {{course_title}}!', 'body' => 'Hi {{student_name}}, you have been enrolled in {{course_title}}. Start learning now!'],
            ['key' => 'enrollment_created_in_app', 'event' => 'enrollment.created', 'channel' => 'in_app', 'subject' => null, 'body' => 'You enrolled in {{course_title}}'],
            ['key' => 'enrollment_completed_email', 'event' => 'enrollment.completed', 'channel' => 'email', 'subject' => 'Congratulations on completing {{course_title}}!', 'body' => 'Well done, {{student_name}}! You have completed {{course_title}} with a score of {{score}}%.'],
            ['key' => 'enrollment_completed_in_app', 'event' => 'enrollment.completed', 'channel' => 'in_app', 'subject' => null, 'body' => 'Course completed: {{course_title}}'],
            ['key' => 'certificate_issued_email', 'event' => 'certificate.issued', 'channel' => 'email', 'subject' => 'Your Certificate for {{course_title}}', 'body' => 'Hi {{student_name}}, your certificate has been issued. Verification code: {{code}}'],
            ['key' => 'certificate_issued_in_app', 'event' => 'certificate.issued', 'channel' => 'in_app', 'subject' => null, 'body' => 'Certificate earned for {{course_title}}!'],
            ['key' => 'payment_succeeded_email', 'event' => 'payment.succeeded', 'channel' => 'email', 'subject' => 'Payment Confirmation', 'body' => 'Your payment of {{amount}} for {{course_title}} was successful.'],
            ['key' => 'payment_succeeded_in_app', 'event' => 'payment.succeeded', 'channel' => 'in_app', 'subject' => null, 'body' => 'Payment of {{amount}} confirmed'],
            ['key' => 'quiz_submitted_in_app', 'event' => 'quiz.submitted', 'channel' => 'in_app', 'subject' => null, 'body' => 'Quiz result available: {{quiz_title}} — Score: {{score}}%'],
        ];

        foreach ($notifTemplates as $tpl) {
            NotificationTemplate::create(array_merge($tpl, ['language' => 'en', 'is_active' => true]));
        }

        // Notification Triggers (defaults)
        $triggerEvents = [
            'enrollment.created' => ['email' => true, 'sms' => false, 'in_app' => true, 'push' => false],
            'enrollment.completed' => ['email' => true, 'sms' => false, 'in_app' => true, 'push' => false],
            'certificate.issued' => ['email' => true, 'sms' => true, 'in_app' => true, 'push' => false],
            'payment.succeeded' => ['email' => true, 'sms' => true, 'in_app' => true, 'push' => false],
            'quiz.submitted' => ['email' => false, 'sms' => false, 'in_app' => true, 'push' => false],
            'assignment.submitted' => ['email' => false, 'sms' => false, 'in_app' => true, 'push' => false],
        ];

        foreach ($triggerEvents as $event => $channels) {
            NotificationTrigger::create([
                'event' => $event,
                'tenant_id' => null,
                'email_enabled' => $channels['email'],
                'sms_enabled' => $channels['sms'],
                'in_app_enabled' => $channels['in_app'],
                'push_enabled' => $channels['push'],
            ]);
        }

        // Tenant Themes (for existing tenants)
        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $tenant) {
            TenantTheme::create([
                'tenant_id' => $tenant->id,
                'primary_color' => '#5A0917',
                'secondary_color' => '#F6891F',
                'email_sender_name' => $tenant->name,
            ]);
        }
    }
}
