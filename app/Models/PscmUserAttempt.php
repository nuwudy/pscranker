<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PscmUserAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'session_id',
        'practice_responses',
        'omr_responses',
        'gamification',
        'completed_at',
    ];

    protected $casts = [
        'practice_responses' => 'array',
        'omr_responses' => 'array',
        'gamification' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(PscmSession::class, 'session_id');
    }
}
