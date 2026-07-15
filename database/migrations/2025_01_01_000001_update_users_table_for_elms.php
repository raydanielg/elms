<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'teacher', 'solo_teacher', 'student'])->default('student')->after('email');
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete()->after('role');
            $table->string('avatar')->nullable()->after('tenant_id');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active')->after('bio');
            $table->json('preferences')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['role', 'tenant_id', 'avatar', 'phone', 'bio', 'status', 'preferences']);
        });
    }
};
