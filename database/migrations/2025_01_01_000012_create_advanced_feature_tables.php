<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Feature Flags
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_global_enabled')->default(true);
            $table->json('plan_ids')->nullable();
            $table->json('tenant_overrides')->nullable();
            $table->timestamps();
        });

        // Dynamic Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->json('roles')->nullable();
            $table->string('parent_key')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Payment Gateways Configuration
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('driver')->unique();
            $table->string('label');
            $table->string('category');
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0);
            $table->json('credentials')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('supported_countries')->nullable();
            $table->timestamps();
        });

        // Payment Webhook Logs
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_id')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('received');
            $table->text('error')->nullable();
            $table->timestamps();
        });

        // SMS Gateways Configuration
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('driver')->unique();
            $table->string('label');
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0);
            $table->json('credentials')->nullable();
            $table->string('sender_id')->nullable();
            $table->timestamps();
        });

        // SMS Templates
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('event');
            $table->string('category')->default('critical');
            $table->string('language')->default('en');
            $table->text('template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SMS Campaigns
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->json('recipient_filters')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // SMS Credits (per tenant)
        Schema::create('sms_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('balance')->default(0);
            $table->integer('total_purchased')->default(0);
            $table->integer('total_used')->default(0);
            $table->timestamps();
        });

        // SMS Opt-Outs
        Schema::create('sms_opt_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->timestamps();
            $table->unique(['user_id', 'category']);
        });

        // Custom Fields
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('form_type');
            $table->string('field_name');
            $table->string('field_label');
            $table->string('field_type');
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Custom Field Data
        Schema::create('custom_field_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $table->morphs('model');
            $table->json('value')->nullable();
            $table->timestamps();
        });

        // Automation Rules
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_event');
            $table->json('conditions')->nullable();
            $table->string('action_class');
            $table->json('action_params')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Currencies
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol');
            $table->string('locale')->nullable();
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tenant Themes
        Schema::create('tenant_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('primary_color')->default('#5A0917');
            $table->string('secondary_color')->default('#F6891F');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('font_family')->nullable();
            $table->string('custom_domain')->nullable();
            $table->string('email_sender_name')->nullable();
            $table->json('custom_css')->nullable();
            $table->timestamps();
        });

        // Notification Templates
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('event');
            $table->string('channel');
            $table->string('language')->default('en');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Trigger Matrix
        Schema::create('notification_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event');
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('in_app_enabled')->default(true);
            $table->boolean('push_enabled')->default(false);
            $table->timestamps();
        });

        // Coupons (check if already created by commerce migration)
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
                $table->string('code')->unique();
                $table->string('type');
                $table->decimal('value', 10, 2);
                $table->decimal('min_purchase', 10, 2)->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->date('starts_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->json('course_ids')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Course Price Rules (promotions, tiered pricing)
        Schema::create('course_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('rule_type');
            $table->json('rule_config')->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Course Versions
        Schema::create('course_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number');
            $table->json('content_snapshot');
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Course Approval Workflows
        Schema::create('course_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('approval_type');
            $table->string('status')->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referral_code')->unique();
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        // Wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        // Featured Courses
        Schema::create('featured_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('placement')->default('marketplace_home');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Webhook Endpoints (outgoing)
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('url');
            $table->json('events')->nullable();
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Webhook Dispatches
        Schema::create('webhook_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_endpoint_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->integer('response_code')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        // API Keys
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key_hash')->unique();
            $table->string('key_prefix')->nullable();
            $table->json('scopes')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SMS Credit Bundles (purchasable)
        Schema::create('sms_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('credits');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('sms_bundles');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('webhook_dispatches');
        Schema::dropIfExists('webhook_endpoints');
        Schema::dropIfExists('featured_courses');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('course_approvals');
        Schema::dropIfExists('course_versions');
        Schema::dropIfExists('course_price_rules');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('notification_triggers');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('tenant_themes');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('automation_rules');
        Schema::dropIfExists('custom_field_data');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('sms_opt_outs');
        Schema::dropIfExists('sms_credits');
        Schema::dropIfExists('sms_campaigns');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_gateways');
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('feature_flags');
    }
};
