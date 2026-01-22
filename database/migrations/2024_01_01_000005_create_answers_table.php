<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('marks_obtained')->default(0);
            $table->timestamps();
            
            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
