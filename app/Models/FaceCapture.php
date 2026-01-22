<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceCapture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'exam_attempt_id', 'capture_type', 'image_path',
        'verification_passed', 'confidence_score', 'liveness_verified',
        'blink_count', 'metadata', 'ip_address', 'user_agent', 
        'device_info', 'browser_info'
    ];

    protected function casts(): array
    {
        return [
            'verification_passed' => 'boolean',
            'liveness_verified' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }
}
