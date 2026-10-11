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

    public const ACCESS_TIERS = [
        'premium' => 'PRO / Paid (Default)',
        'registered' => 'Free Registered (Login Required)',
        'free' => 'Free (Open to Everyone)',
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
        'access_level',
        'price',
    ];

    protected $casts = [
        'hook_question' => 'array',
        'media' => 'array',
        'practice_mcqs' => 'array',
        'omr_config' => 'array',
        'price' => 'decimal:2',
    ];

    public function isFree(): bool
    {
        return ($this->access_level ?? 'premium') === 'free';
    }

    public function isRegisteredOnly(): bool
    {
        return ($this->access_level ?? 'premium') === 'registered';
    }

    public function isPremiumOnly(): bool
    {
        return ($this->access_level ?? 'premium') === 'premium';
    }

    public function canAccess(?User $user): bool
    {
        if ($this->isFree()) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isRegisteredOnly()) {
            return true;
        }

        // Must be subscribed for premium
        return $user->isSubscribed();
    }
}
