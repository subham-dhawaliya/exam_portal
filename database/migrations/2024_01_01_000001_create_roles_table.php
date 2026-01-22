<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add role_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(2)->after('id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_image')->nullable()->after('address');
            $table->string('reference_face_image')->nullable()->after('profile_image');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('reference_face_image');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'address', 'profile_image', 'reference_face_image', 'status', 'last_login_at']);
        });
        Schema::dropIfExists('roles');
    }
};
