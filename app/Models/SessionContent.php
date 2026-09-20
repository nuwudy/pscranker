<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionContent extends Model
{
    use HasFactory;

    protected $table = 'session_contents';

    protected $fillable = [
        'session_id',
        'unit_order',
        'unit_title',
        'type',
        'content_data',
        'order',
    ];

    protected $casts = [
        'unit_order' => 'integer',
        'content_data' => 'array',
        'order' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
