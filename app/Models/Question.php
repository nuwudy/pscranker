<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'quiz_id',
        'question_text',
        'question_text_malayalam',
        'options',
        'correct_option',
        'explanation',
        'explanation_malayalam',
        'trap_warning',
        'meme_image_url',
        'psc_exam_reference',
        'difficulty',
        'points',
        'negative_points',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'decimal:2',
        'negative_points' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
