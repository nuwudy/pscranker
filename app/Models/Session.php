<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Session extends Model
{
    use HasFactory;

    protected $table = 'learning_sessions';

    protected $fillable = [
        'title',
        'title_malayalam',
        'slug',
        'category_id',
        'order',
        'xp_reward',
        'is_active',
        'is_premium',
        'price',
        'in_general_stream',
        'general_stream_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'order' => 'integer',
        'xp_reward' => 'integer',
        'in_general_stream' => 'boolean',
        'general_stream_order' => 'integer',
    ];

    public function isFree(): bool
    {
        return !$this->is_premium;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->isFree() || !$this->price) {
            return 'FREE';
        }
        return '₹' . number_format($this->price, 0);
    }

    public function getPreviousSession(?string $stream = null): ?self
    {
        if ($stream === 'general' && $this->in_general_stream) {
            $prev = self::where('is_active', true)
                ->where('in_general_stream', true)
                ->where('general_stream_order', '<', $this->general_stream_order ?? 999999)
                ->orderBy('general_stream_order', 'desc')
                ->first();
            if ($prev) {
                return $prev;
            }
        }

        return self::where('is_active', true)
            ->where(function ($q) {
                if ($this->category_id) {
                    $q->where('category_id', $this->category_id);
                }
            })
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first() 
            ?? self::where('is_active', true)
                ->where('order', '<', $this->order)
                ->orderBy('order', 'desc')
                ->first();
    }

    public function getNextSession(?string $stream = null): ?self
    {
        if ($stream === 'general' && $this->in_general_stream) {
            $next = self::where('is_active', true)
                ->where('in_general_stream', true)
                ->where('general_stream_order', '>', $this->general_stream_order ?? 0)
                ->orderBy('general_stream_order', 'asc')
                ->first();
            if ($next) {
                return $next;
            }
        }

        return self::where('is_active', true)
            ->where(function ($q) {
                if ($this->category_id) {
                    $q->where('category_id', $this->category_id);
                }
            })
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first()
            ?? self::where('is_active', true)
                ->where('order', '>', $this->order)
                ->orderBy('order', 'asc')
                ->first();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(SessionContent::class, 'session_id')->orderBy('order', 'asc');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id');
    }

    public function diagnosticQuestion(): HasOne
    {
        return $this->hasOne(Question::class, 'session_id')->where('phase_type', 'diagnostic');
    }

    public function reinforcementQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id')->where('phase_type', 'reinforcement');
    }

    public function omrQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id')->where('phase_type', 'omr');
    }

    public function getEffectiveOmrQuestionsAttribute()
    {
        return $this->omrQuestions->isNotEmpty()
            ? $this->omrQuestions
            : $this->reinforcementQuestions;
    }

    public function getEffectiveReinforcementQuestionsAttribute()
    {
        return $this->reinforcementQuestions->isNotEmpty()
            ? $this->reinforcementQuestions
            : $this->omrQuestions;
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserSessionProgress::class, 'session_id');
    }
}
