<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'phone', 'address',
        'profile_image', 'reference_face_image', 'face_descriptor', 'status', 'last_login_at',
        'face_verified', 'face_enrollment_count', 'face_enrolled_at',
        'failed_face_attempts', 'face_locked_until'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'face_enrolled_at' => 'datetime',
            'face_locked_until' => 'datetime',
            'face_verified' => 'boolean',
            'face_descriptor' => 'array',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function faceCaptures(): HasMany
    {
        return $this->hasMany(FaceCapture::class);
    }

    public function faceEmbeddings(): HasMany
    {
        return $this->hasMany(FaceEmbedding::class);
    }

    public function faceVerificationLogs(): HasMany
    {
        return $this->hasMany(FaceVerificationLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->slug === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role && $this->role->slug === 'user';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFaceVerified(): bool
    {
        return $this->face_verified && $this->faceEmbeddings()->where('is_active', true)->exists();
    }

    public function isFaceLocked(): bool
    {
        return $this->face_locked_until && $this->face_locked_until->isFuture();
    }

    public function incrementFailedFaceAttempts(): void
    {
        $this->increment('failed_face_attempts');
        
        // Lock after 5 failed attempts for 15 minutes
        if ($this->failed_face_attempts >= 5) {
            $this->update(['face_locked_until' => now()->addMinutes(15)]);
        }
    }

    public function resetFailedFaceAttempts(): void
    {
        $this->update([
            'failed_face_attempts' => 0,
            'face_locked_until' => null,
        ]);
    }
}
