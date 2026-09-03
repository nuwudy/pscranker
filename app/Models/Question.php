<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'quiz_id',
        'session_id',
        'phase_type',
        'question_text',
        'question_text_malayalam',
        'options',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'explanation',
        'explanation_malayalam',
        'trap_warning',
        'trap_warning_text',
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

    protected $appends = [
        'resolved_options',
        'resolved_trap_warning',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Resolve options array from either individual option_a..d or options JSON.
     */
    public function getResolvedOptionsAttribute(): array
    {
        if (!empty($this->options) && is_array($this->options)) {
            return $this->options;
        }

        $opts = [];
        if ($this->option_a !== null) $opts[] = ['key' => 'A', 'text' => $this->option_a];
        if ($this->option_b !== null) $opts[] = ['key' => 'B', 'text' => $this->option_b];
        if ($this->option_c !== null) $opts[] = ['key' => 'C', 'text' => $this->option_c];
        if ($this->option_d !== null) $opts[] = ['key' => 'D', 'text' => $this->option_d];

        return $opts;
    }

    /**
     * Resolve trap warning from either trap_warning_text or trap_warning.
     */
    public function getResolvedTrapWarningAttribute(): ?string
    {
        return $this->trap_warning_text ?: $this->trap_warning;
    }
}
