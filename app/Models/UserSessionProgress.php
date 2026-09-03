<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSessionProgress extends Model
{
    use HasFactory;

    protected $table = 'user_session_progress';

    protected $fillable = [
        'user_id',
        'guest_token',
        'session_id',
        'current_phase',
        'diagnostic_status',
        'reinforcement_score',
        'omr_score',
        'net_marks',
        'xp_earned',
        'time_taken_seconds',
        'completed_at',
    ];

    protected $casts = [
        'reinforcement_score' => 'decimal:2',
        'omr_score' => 'decimal:2',
        'net_marks' => 'decimal:2',
        'xp_earned' => 'integer',
        'time_taken_seconds' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
