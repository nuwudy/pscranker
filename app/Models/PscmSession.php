<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PscmSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'slug',
        'topic_id',
        'subject',
        'hook_question',
        'media',
        'lesson_content',
        'practice_mcqs',
        'omr_config',
        'status',
    ];

    protected $casts = [
        'hook_question' => 'array',
        'media' => 'array',
        'practice_mcqs' => 'array',
        'omr_config' => 'array',
    ];
}
