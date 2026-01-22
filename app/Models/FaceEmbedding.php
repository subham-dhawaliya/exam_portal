<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceEmbedding extends Model
{
    protected $fillable = [
        'user_id',
        'embedding_data',
        'embedding_hash',
        'quality_score',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getActiveEmbeddings($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->get();
    }
}
