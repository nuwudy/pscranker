<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'title_malayalam',
        'slug',
        'description',
        'time_limit_seconds',
        'question_time_limit',
        'total_marks',
        'negative_marking_rate',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'time_limit_seconds' => 'integer',
        'question_time_limit' => 'integer',
        'total_marks' => 'decimal:2',
        'negative_marking_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(DrillAttempt::class);
    }
}
