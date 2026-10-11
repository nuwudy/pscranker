<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PscmSession extends Model
{
    use HasFactory, HasUuids;

    public const CATEGORIES = [
        'GK' => 'General Knowledge (GK)',
        'History' => 'History & Kerala Renaissance',
        'Geography' => 'Geography & Environment',
        'English' => 'General English',
        'Malayalam' => 'Malayalam Language',
        'Constitution' => 'Indian Constitution & Polity',
        'Science' => 'General Science & Tech',
        'Maths' => 'Simple Arithmetic & Mental Ability',
        'Current Affairs' => 'Current Affairs',
    ];

    protected $fillable = [
        'title',
        'category',
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
