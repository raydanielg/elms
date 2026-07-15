<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived', 'pending_review'])->default('draft');
            $table->enum('visibility', ['private', 'marketplace'])->default('private');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->string('language')->default('English');
            $table->boolean('drip_enabled')->default(false);
            $table->integer('drip_days')->default(0);
            $table->boolean('has_certificate')->default(true);
            $table->integer('duration_hours')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('content_type', ['video', 'pdf', 'text', 'link', 'iframe'])->default('text');
            $table->string('video_url')->nullable();
            $table->string('file_path')->nullable();
            $table->longText('text_content')->nullable();
            $table->string('external_link')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
    }
};
