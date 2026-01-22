<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceVerificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'verification_type',
        'status',
        'match_score',
        'liveness_score',
        'quality_score',
        'failure_reason',
        'liveness_checks',
        'ip_address',
        'user_agent',
        'device_fingerprint',
    ];

    protected $casts = [
        'liveness_checks' => 'array',
        'match_score' => 'decimal:2',
        'liveness_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logAttempt($data)
    {
        return self::create(array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]));
    }
}
