<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend existing certificates table
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->after('tenant_id')->constrained('certificate_templates')->nullOnDelete();
            $table->string('type')->default('course_completion')->after('template_id');
            $table->string('status')->default('valid')->after('type');
            $table->string('title')->nullable()->after('status');
            $table->string('pdf_path')->nullable()->after('final_score');
            $table->string('social_image_path')->nullable()->after('pdf_path');
            $table->string('signature_path')->nullable()->after('social_image_path');
            $table->string('signature_title')->nullable()->after('signature_path');
            $table->string('data_hash')->nullable()->after('signature_title');
            $table->json('metadata')->nullable()->after('data_hash');
            $table->foreignId('issued_by')->nullable()->after('metadata')->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable()->after('issued_at');
            $table->text('revocation_reason')->nullable()->after('revoked_at');
        });

        // Certificate Templates
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('course_completion');
            $table->string('layout')->default('classic');
            $table->json('design_config')->nullable();
            $table->string('background_image')->nullable();
            $table->string('font_family')->nullable();
            $table->string('primary_color')->default('#5A0917');
            $table->string('secondary_color')->default('#F6891F');
            $table->boolean('show_grade')->default(true);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_logo')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        // Badges
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('category');
            $table->string('color')->default('#F6891F');
            $table->integer('xp_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Badge Rules
        Schema::create('badge_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_event');
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Badges (pivot)
        Schema::create('student_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'badge_id']);
        });

        // Awards
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type');
            $table->foreignId('awarded_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('certificate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        // Points Ledger
        Schema::create('points_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('description')->nullable();
            $table->integer('points');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // Levels
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('level_number');
            $table->string('name');
            $table->integer('min_xp')->default(0);
            $table->integer('max_xp')->default(0);
            $table->string('icon')->nullable();
            $table->json('perks')->nullable();
            $table->timestamps();
        });

        // Leaderboard Snapshots
        Schema::create('leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('scope');
            $table->string('period')->default('all_time');
            $table->json('rankings')->nullable();
            $table->timestamp('snapshot_at')->useCurrent();
            $table->timestamps();
        });

        // Transcripts
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('verification_code')->unique();
            $table->string('pdf_path')->nullable();
            $table->string('status')->default('active');
            $table->string('grading_scale')->default('percentage');
            $table->json('data_snapshot')->nullable();
            $table->string('data_hash')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Verification Logs
        Schema::create('verification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code');
            $table->string('verifiable_type');
            $table->unsignedBigInteger('verifiable_id');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_logs');
        Schema::dropIfExists('transcripts');
        Schema::dropIfExists('leaderboard_snapshots');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('points_ledger');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('student_badges');
        Schema::dropIfExists('badge_rules');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('certificate_templates');

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['template_id']);
            $table->dropForeign(['issued_by']);
            $table->dropColumn([
                'tenant_id', 'template_id', 'type', 'status', 'title', 'pdf_path',
                'social_image_path', 'signature_path', 'signature_title', 'data_hash',
                'metadata', 'issued_by', 'revoked_at', 'revocation_reason'
            ]);
        });
    }
};
