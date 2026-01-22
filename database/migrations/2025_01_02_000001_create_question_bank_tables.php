<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sections table for organizing questions
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon class or emoji
            $table->string('color')->default('#3B82F6'); // Section color
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Question Bank - standalone questions not tied to specific exam
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->enum('question_type', [
                'mcq',              // Single choice
                'multiple_select',  // Multiple correct answers
                'true_false',       // True or False
                'fill_blank',       // Fill in the blank
                'short_answer',     // Short text answer
                'match_following',  // Match pairs
                'ordering',         // Arrange in sequence
                'numerical',        // Number with tolerance
                'essay'             // Long answer (manual grading)
            ])->default('mcq');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 5, 2)->default(1);
            $table->decimal('negative_marks', 5, 2)->default(0);
            $table->integer('time_limit')->nullable(); // Seconds per question
            $table->text('explanation')->nullable();
            $table->json('tags')->nullable(); // For searching/filtering
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['section_id', 'question_type', 'difficulty']);
        });

        // Options for MCQ and Multiple Select questions
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->text('option_text');
            $table->string('option_image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Acceptable answers for Fill Blank, Short Answer, Numerical
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->text('answer_text');
            $table->boolean('is_case_sensitive')->default(false);
            $table->boolean('allow_partial_match')->default(false);
            $table->decimal('tolerance', 10, 4)->nullable(); // For numerical questions
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Matching pairs for Match the Following
        Schema::create('question_match_pairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->text('left_side');
            $table->text('right_side');
            $table->string('left_image')->nullable();
            $table->string('right_image')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Ordering items for sequence questions
        Schema::create('question_ordering_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->text('item_text');
            $table->string('item_image')->nullable();
            $table->integer('correct_position'); // The correct order position
            $table->timestamps();
        });

        // Link table: Exam to Question Bank (for selecting questions from bank)
        Schema::create('exam_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->decimal('marks_override', 5, 2)->nullable(); // Override marks for this exam
            $table->timestamps();
            
            $table->unique(['exam_id', 'question_bank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_question_bank');
        Schema::dropIfExists('question_ordering_items');
        Schema::dropIfExists('question_match_pairs');
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('question_bank');
        Schema::dropIfExists('sections');
    }
};
