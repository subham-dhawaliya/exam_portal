<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionMatchPair extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id', 'left_side', 'right_side', 
        'left_image', 'right_image', 'order'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }
}
