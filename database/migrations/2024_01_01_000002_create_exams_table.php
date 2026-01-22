<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->enum('status', ['draft', 'published', 'active', 'completed', 'archived'])->default('draft');
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_results')->default(true);
            $table->boolean('face_verification_required')->default(true);
            $table->integer('max_attempts')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
