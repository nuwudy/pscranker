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
        'feature_image',
        'feature_video',
        'slug',
        'category_id',
        'order',
        'xp_reward',
        'is_active',
        'access_level',
        'is_premium',
        'price',
        'in_general_stream',
        'general_stream_order',
        'creation_mode',
        'custom_html',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'access_level' => 'string',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'order' => 'integer',
        'xp_reward' => 'integer',
        'in_general_stream' => 'boolean',
        'general_stream_order' => 'integer',
        'creation_mode' => 'string',
    ];

    public function isCustomCode(): bool
    {
        return ($this->creation_mode ?? 'manual') === 'code';
    }

    public function isManual(): bool
    {
        return !$this->isCustomCode();
    }

    public function hasFeatureMedia(): bool
    {
        return !empty($this->feature_video) || !empty($this->feature_image);
    }

    public function isFeatureVideoEmbed(): bool
    {
        if (empty($this->feature_video)) {
            return false;
        }

        $url = strtolower($this->feature_video);
        return str_contains($url, 'youtube.com') 
            || str_contains($url, 'youtu.be') 
            || str_contains($url, 'vimeo.com');
    }

    public function getFeatureVideoEmbedUrl(): string
    {
        if (empty($this->feature_video)) {
            return '';
        }

        $url = trim($this->feature_video);

        // YouTube matches (watch?v=, youtu.be/, embed/, shorts/)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]+)/i', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        }

        // Vimeo matches
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $url, $matches)) {
            $vimeoId = end($matches);
            return 'https://player.vimeo.com/video/' . $vimeoId;
        }

        return $url;
    }

    public function isGuest(): bool
    {
        return ($this->access_level ?? 'guest') === 'guest';
    }

    public function isRegisteredOnly(): bool
    {
        return ($this->access_level ?? 'guest') === 'registered';
    }

    public function isPremiumOnly(): bool
    {
        return ($this->access_level ?? 'guest') === 'premium' || $this->is_premium;
    }

    public function isFree(): bool
    {
        return !$this->isPremiumOnly();
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
