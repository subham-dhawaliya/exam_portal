<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['mcq', 'descriptive'])->default('mcq');
            $table->text('question');
            $table->json('options')->nullable(); // For MCQ options
            $table->text('correct_answer')->nullable();
            $table->integer('marks')->default(1);
            $table->integer('order')->default(0);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
