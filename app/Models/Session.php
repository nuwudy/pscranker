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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'xp_reward' => 'integer',
    ];

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
