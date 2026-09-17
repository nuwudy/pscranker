<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin',
        'subscribed_until',
        'subscription_plan',
        'subscription_amount',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'subscribed_until' => 'datetime',
            'subscription_amount' => 'decimal:2',
        ];
    }

    /**
     * Check whether user has admin privileges.
     */
    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin || $this->email === 'admin@pscranker.com' || $this->phone === '9895940500');
    }

    /**
     * Check whether user has active prepaid subscription or is admin.
     */
     public function isSubscribed(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->subscribed_until && $this->subscribed_until->isFuture();
    }

    /**
     * Alias for isSubscribed to represent the Premium tier.
     */
    public function isPremium(): bool
    {
        return $this->isSubscribed();
    }

    /**
     * Check whether user is a registered member.
     */
    public function isRegistered(): bool
    {
        return true;
    }

    /**
     * Get remaining days in prepaid subscription.
     */
    public function subscriptionDaysRemaining(): int
    {
        if ($this->isAdmin()) {
            return 999;
        }

        if (!$this->subscribed_until || $this->subscribed_until->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->subscribed_until, false);
    }

    /**
     * Get the affiliate profile associated with this user.
     */
    public function affiliate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Affiliate::class);
    }

    /**
     * Check if user is an approved active affiliate promoter.
     */
    public function isAffiliate(): bool
    {
        return $this->affiliate !== null && $this->affiliate->status === 'active';
    }
}

