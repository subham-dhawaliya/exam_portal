<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOrderingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id', 'item_text', 'item_image', 'correct_position'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }
}
