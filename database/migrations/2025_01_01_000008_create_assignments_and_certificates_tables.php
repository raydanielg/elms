<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('instructions')->nullable();
            $table->integer('max_points')->default(100);
            $table->timestamp('due_date')->nullable();
            $table->boolean('allow_late_submission')->default(true);
            $table->integer('late_penalty_percent')->default(10);
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_file_size_mb')->default(10);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('submission_text')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned', 'late'])->default('submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('verification_code')->unique();
            $table->decimal('final_score', 5, 2)->default(0);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
