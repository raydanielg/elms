<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->enum('audience', ['all', 'students', 'teachers', 'course'])->default('all');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications');
    }
};
