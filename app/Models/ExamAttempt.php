<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'exam_id', 'started_at', 'completed_at', 'score', 'total_marks',
        'percentage', 'status', 'passed', 'tab_switch_count', 'ip_address', 'user_agent'
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'passed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function faceCaptures(): HasMany
    {
        return $this->hasMany(FaceCapture::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getRemainingTime(): int
    {
        if (!$this->isInProgress()) return 0;
        
        $endTime = $this->started_at->addMinutes($this->exam->duration_minutes);
        $remaining = now()->diffInSeconds($endTime, false);
        
        return max(0, $remaining);
    }

    public function calculateScore(): void
    {
        $score = $this->answers()->where('is_correct', true)->sum('marks_obtained');
        $totalMarks = $this->exam->total_marks;
        $percentage = $totalMarks > 0 ? ($score / $totalMarks) * 100 : 0;
        
        $this->update([
            'score' => $score,
            'total_marks' => $totalMarks,
            'percentage' => round($percentage, 2),
            'passed' => $score >= $this->exam->passing_marks,
        ]);
    }
}
