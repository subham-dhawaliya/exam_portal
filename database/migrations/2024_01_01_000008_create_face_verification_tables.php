<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Face embeddings storage
        Schema::create('face_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('embedding_data')->comment('Encrypted face embedding vector');
            $table->string('embedding_hash')->comment('Hash for quick comparison');
            $table->integer('quality_score')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });

        // Face verification attempts log
        Schema::create('face_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('email')->nullable();
            $table->enum('verification_type', ['login', 'registration', 'exam_start', 're_enrollment']);
            $table->enum('status', ['success', 'failed', 'blocked', 'no_face', 'spoof_detected', 'low_quality']);
            $table->decimal('match_score', 5, 2)->nullable();
            $table->decimal('liveness_score', 5, 2)->nullable();
            $table->decimal('quality_score', 5, 2)->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('liveness_checks')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['created_at']);
        });

        // Add face verification columns to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('face_verified')->default(false)->after('reference_face_image');
            $table->integer('face_enrollment_count')->default(0)->after('face_verified');
            $table->timestamp('face_enrolled_at')->nullable()->after('face_enrollment_count');
            $table->integer('failed_face_attempts')->default(0)->after('face_enrolled_at');
            $table->timestamp('face_locked_until')->nullable()->after('failed_face_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_verified', 'face_enrollment_count', 'face_enrolled_at', 'failed_face_attempts', 'face_locked_until']);
        });
        Schema::dropIfExists('face_verification_logs');
        Schema::dropIfExists('face_embeddings');
    }
};
