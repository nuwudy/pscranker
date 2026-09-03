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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'order' => 'integer',
        'xp_reward' => 'integer',
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

    public function getPreviousSession(): ?self
    {
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

    public function getNextSession(): ?self
    {
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

    public function progress(): HasMany
    {
        return $this->hasMany(UserSessionProgress::class, 'session_id');
    }
}
