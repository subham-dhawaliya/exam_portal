<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestionBank extends Model
{
    protected $table = 'exam_question_bank';

    protected $fillable = [
        'exam_id',
        'question_bank_id',
        'order',
        'marks_override',
    ];

    protected function casts(): array
    {
        return [
            'marks_override' => 'decimal:2',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }
}
