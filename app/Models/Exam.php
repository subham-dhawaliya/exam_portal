<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by', 'title', 'description', 'duration_minutes', 'total_marks',
        'passing_marks', 'start_time', 'end_time', 'status', 'shuffle_questions',
        'show_results', 'face_verification_required', 'max_attempts'
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'shuffle_questions' => 'boolean',
            'show_results' => 'boolean',
            'face_verification_required' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function questionBankLinks(): HasMany
    {
        return $this->hasMany(ExamQuestionBank::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isAvailable(): bool
    {
        $now = now();
        return $this->status === 'published' &&
               (!$this->start_time || $now->gte($this->start_time)) &&
               (!$this->end_time || $now->lte($this->end_time));
    }

    public function userAttemptCount(User $user): int
    {
        return $this->attempts()->where('user_id', $user->id)->count();
    }

    public function canUserAttempt(User $user): bool
    {
        return $this->isAvailable() && $this->userAttemptCount($user) < $this->max_attempts;
    }
}
