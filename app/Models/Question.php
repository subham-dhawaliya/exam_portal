<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'type', 'question', 'options', 'correct_answer', 'marks', 'order', 'explanation'
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isMcq(): bool
    {
        return $this->type === 'mcq';
    }

    public function isDescriptive(): bool
    {
        return $this->type === 'descriptive';
    }

    public function checkAnswer(string $answer): bool
    {
        if ($this->isMcq()) {
            return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
        }
        return false; // Descriptive answers need manual grading
    }
}
