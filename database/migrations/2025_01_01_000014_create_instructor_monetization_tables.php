<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend courses table with pricing_model
        if (!Schema::hasColumn('courses', 'pricing_model')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('pricing_model')->default('one_time')->after('price');
                $table->string('subscription_interval')->nullable()->after('pricing_model');
                $table->decimal('subscription_price', 10, 2)->nullable()->after('subscription_interval');
                $table->boolean('is_paid_certificate')->default(false)->after('subscription_price');
                $table->decimal('certificate_price', 10, 2)->nullable()->after('is_paid_certificate');
            });
        }

        // Extend wallets with pending_balance
        if (!Schema::hasColumn('wallets', 'pending_balance')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->decimal('pending_balance', 10, 2)->default(0)->after('balance');
                $table->string('currency', 3)->default('USD')->after('pending_balance');
            });
        }

        // Extend transactions with commission breakdown
        if (!Schema::hasColumn('transactions', 'course_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
                $table->foreignId('instructor_id')->nullable()->after('course_id')->constrained('users')->nullOnDelete();
                $table->decimal('gross_amount', 10, 2)->nullable()->after('amount');
                $table->decimal('commission_amount', 10, 2)->default(0)->after('gross_amount');
                $table->decimal('commission_rate_applied', 5, 2)->default(0)->after('commission_amount');
                $table->decimal('gateway_fee', 10, 2)->default(0)->after('commission_rate_applied');
                $table->decimal('tax_amount', 10, 2)->default(0)->after('gateway_fee');
                $table->decimal('net_amount', 10, 2)->nullable()->after('tax_amount');
                $table->foreignId('coupon_id')->nullable()->after('metadata')->constrained()->nullOnDelete();
                $table->foreignId('referral_id')->nullable()->after('coupon_id')->constrained('referrals')->nullOnDelete();
                $table->string('instructor_level_at_sale')->nullable()->after('referral_id');
            });
        }

        // Instructor Levels
        Schema::create('instructor_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level_number');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('min_sales')->default(0);
            $table->decimal('min_rating', 3, 2)->default(0);
            $table->decimal('max_refund_rate', 5, 2)->default(100);
            $table->decimal('commission_rate', 5, 2)->default(25);
            $table->integer('payout_speed_days')->default(7);
            $table->string('badge_icon')->nullable();
            $table->string('badge_color')->default('#F6891F');
            $table->json('perks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Instructor Level History
        Schema::create('instructor_level_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_level_id')->constrained()->cascadeOnDelete();
            $table->integer('previous_level_id')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('is_manual_override')->default(false);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Revenue Shares (institution-teacher internal split)
        Schema::create('revenue_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('institution_percentage', 5, 2)->default(40);
            $table->decimal('teacher_percentage', 5, 2)->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('revenue_shares');
        Schema::dropIfExists('instructor_level_history');
        Schema::dropIfExists('instructor_levels');

        if (Schema::hasColumn('transactions', 'course_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropForeign(['instructor_id']);
                $table->dropForeign(['coupon_id']);
                $table->dropForeign(['referral_id']);
                $table->dropColumn([
                    'course_id', 'instructor_id', 'gross_amount', 'commission_amount',
                    'commission_rate_applied', 'gateway_fee', 'tax_amount', 'net_amount',
                    'coupon_id', 'referral_id', 'instructor_level_at_sale'
                ]);
            });
        }

        if (Schema::hasColumn('wallets', 'pending_balance')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropColumn(['pending_balance', 'currency']);
            });
        }

        if (Schema::hasColumn('courses', 'pricing_model')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn([
                    'pricing_model', 'subscription_interval', 'subscription_price',
                    'is_paid_certificate', 'certificate_price'
                ]);
            });
        }
    }
};
