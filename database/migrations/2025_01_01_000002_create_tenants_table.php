<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['institution', 'solo'])->default('institution');
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('logo')->nullable();
            $table->string('domain')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'suspended', 'trialing', 'cancelled'])->default('trialing');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
