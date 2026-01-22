<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id', 'answer_text', 'is_case_sensitive', 
        'allow_partial_match', 'tolerance', 'order'
    ];

    protected function casts(): array
    {
        return [
            'is_case_sensitive' => 'boolean',
            'allow_partial_match' => 'boolean',
            'tolerance' => 'decimal:4',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }
}
