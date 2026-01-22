<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->datetime('started_at');
            $table->datetime('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->integer('total_marks')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'timed_out'])->default('in_progress');
            $table->boolean('passed')->nullable();
            $table->integer('tab_switch_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
