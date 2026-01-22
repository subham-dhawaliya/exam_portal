<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'section_id', 'question_type', 'question_text', 'question_image',
        'difficulty', 'marks', 'negative_marks', 'time_limit',
        'explanation', 'tags', 'is_active', 'created_by'
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'negative_marks' => 'decimal:2',
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Question types
    const TYPE_MCQ = 'mcq';
    const TYPE_MULTIPLE_SELECT = 'multiple_select';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_FILL_BLANK = 'fill_blank';
    const TYPE_SHORT_ANSWER = 'short_answer';
    const TYPE_MATCH_FOLLOWING = 'match_following';
    const TYPE_ORDERING = 'ordering';
    const TYPE_NUMERICAL = 'numerical';
    const TYPE_ESSAY = 'essay';

    public static function getQuestionTypes(): array
    {
        return [
            self::TYPE_MCQ => 'Multiple Choice (Single)',
            self::TYPE_MULTIPLE_SELECT => 'Multiple Select',
            self::TYPE_TRUE_FALSE => 'True / False',
            self::TYPE_FILL_BLANK => 'Fill in the Blank',
            self::TYPE_SHORT_ANSWER => 'Short Answer',
            self::TYPE_MATCH_FOLLOWING => 'Match the Following',
            self::TYPE_ORDERING => 'Arrange in Order',
            self::TYPE_NUMERICAL => 'Numerical',
            self::TYPE_ESSAY => 'Essay / Long Answer',
        ];
    }

    public static function getDifficultyLevels(): array
    {
        return [
            'easy' => ['label' => 'Easy', 'color' => 'green'],
            'medium' => ['label' => 'Medium', 'color' => 'yellow'],
            'hard' => ['label' => 'Hard', 'color' => 'red'],
        ];
    }

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_bank_id')->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_bank_id')->orderBy('order');
    }

    public function matchPairs(): HasMany
    {
        return $this->hasMany(QuestionMatchPair::class, 'question_bank_id')->orderBy('order');
    }

    public function orderingItems(): HasMany
    {
        return $this->hasMany(QuestionOrderingItem::class, 'question_bank_id')->orderBy('correct_position');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question_bank')
            ->withPivot('order', 'marks_override')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Helper methods
    public function isAutoGradable(): bool
    {
        return !in_array($this->question_type, [self::TYPE_ESSAY, self::TYPE_SHORT_ANSWER]);
    }

    public function getCorrectAnswers(): array
    {
        switch ($this->question_type) {
            case self::TYPE_MCQ:
                return $this->options()->where('is_correct', true)->pluck('id')->toArray();
            
            case self::TYPE_MULTIPLE_SELECT:
                return $this->options()->where('is_correct', true)->pluck('id')->toArray();
            
            case self::TYPE_TRUE_FALSE:
                $correct = $this->options()->where('is_correct', true)->first();
                return $correct ? [$correct->option_text] : [];
            
            case self::TYPE_FILL_BLANK:
            case self::TYPE_SHORT_ANSWER:
            case self::TYPE_NUMERICAL:
                return $this->answers()->pluck('answer_text')->toArray();
            
            case self::TYPE_MATCH_FOLLOWING:
                return $this->matchPairs()->get()->mapWithKeys(function ($pair) {
                    return [$pair->id => $pair->right_side];
                })->toArray();
            
            case self::TYPE_ORDERING:
                return $this->orderingItems()->orderBy('correct_position')->pluck('id')->toArray();
            
            default:
                return [];
        }
    }

    public function checkAnswer($userAnswer): array
    {
        $result = ['is_correct' => false, 'score' => 0, 'feedback' => ''];

        switch ($this->question_type) {
            case self::TYPE_MCQ:
                $correctOption = $this->options()->where('is_correct', true)->first();
                $result['is_correct'] = $correctOption && $userAnswer == $correctOption->id;
                $result['score'] = $result['is_correct'] ? $this->marks : -$this->negative_marks;
                break;

            case self::TYPE_MULTIPLE_SELECT:
                $correctIds = $this->options()->where('is_correct', true)->pluck('id')->toArray();
                $userIds = is_array($userAnswer) ? $userAnswer : [];
                sort($correctIds);
                sort($userIds);
                $result['is_correct'] = $correctIds === $userIds;
                // Partial scoring
                if (!$result['is_correct'] && !empty($userIds)) {
                    $correct = count(array_intersect($correctIds, $userIds));
                    $wrong = count(array_diff($userIds, $correctIds));
                    $result['score'] = ($correct / count($correctIds)) * $this->marks - ($wrong * $this->negative_marks);
                } else {
                    $result['score'] = $result['is_correct'] ? $this->marks : -$this->negative_marks;
                }
                break;

            case self::TYPE_TRUE_FALSE:
                $correctOption = $this->options()->where('is_correct', true)->first();
                $result['is_correct'] = $correctOption && strtolower($userAnswer) === strtolower($correctOption->option_text);
                $result['score'] = $result['is_correct'] ? $this->marks : -$this->negative_marks;
                break;

            case self::TYPE_FILL_BLANK:
            case self::TYPE_SHORT_ANSWER:
                foreach ($this->answers as $answer) {
                    $correct = $answer->is_case_sensitive 
                        ? $userAnswer === $answer->answer_text
                        : strtolower(trim($userAnswer)) === strtolower(trim($answer->answer_text));
                    
                    if (!$correct && $answer->allow_partial_match) {
                        $correct = $answer->is_case_sensitive
                            ? str_contains($userAnswer, $answer->answer_text)
                            : str_contains(strtolower($userAnswer), strtolower($answer->answer_text));
                    }
                    
                    if ($correct) {
                        $result['is_correct'] = true;
                        break;
                    }
                }
                $result['score'] = $result['is_correct'] ? $this->marks : -$this->negative_marks;
                break;

            case self::TYPE_NUMERICAL:
                $answer = $this->answers()->first();
                if ($answer) {
                    $correctValue = floatval($answer->answer_text);
                    $userValue = floatval($userAnswer);
                    $tolerance = floatval($answer->tolerance ?? 0);
                    $result['is_correct'] = abs($correctValue - $userValue) <= $tolerance;
                }
                $result['score'] = $result['is_correct'] ? $this->marks : -$this->negative_marks;
                break;

            case self::TYPE_MATCH_FOLLOWING:
                $pairs = $this->matchPairs;
                $correctCount = 0;
                $userMatches = is_array($userAnswer) ? $userAnswer : [];
                foreach ($pairs as $pair) {
                    if (isset($userMatches[$pair->id]) && $userMatches[$pair->id] == $pair->right_side) {
                        $correctCount++;
                    }
                }
                $result['is_correct'] = $correctCount === $pairs->count();
                $result['score'] = ($correctCount / max(1, $pairs->count())) * $this->marks;
                break;

            case self::TYPE_ORDERING:
                $correctOrder = $this->orderingItems()->orderBy('correct_position')->pluck('id')->toArray();
                $userOrder = is_array($userAnswer) ? $userAnswer : [];
                $result['is_correct'] = $correctOrder === $userOrder;
                $result['score'] = $result['is_correct'] ? $this->marks : 0;
                break;

            case self::TYPE_ESSAY:
                $result['feedback'] = 'Requires manual grading';
                $result['score'] = 0;
                break;
        }

        return $result;
    }
}
