<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrillAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'session_id',
        'candidate_name',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'score',
        'accuracy_percentage',
        'time_taken_seconds',
        'answers_summary',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'accuracy_percentage' => 'decimal:2',
        'answers_summary' => 'array',
        'completed_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
