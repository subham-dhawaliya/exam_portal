<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_captures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_attempt_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('capture_type', ['login', 'exam_start', 'exam_verification', 'registration']);
            $table->string('image_path');
            $table->boolean('verification_passed')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->boolean('liveness_verified')->default(false);
            $table->integer('blink_count')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->string('browser_info')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'capture_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_captures');
    }
};
