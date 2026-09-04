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
        'password',
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
            'subscribed_until' => 'datetime',
            'subscription_amount' => 'decimal:2',
        ];
    }

    /**
     * Check whether user has active prepaid subscription or is admin.
     */
    public function isSubscribed(): bool
    {
        if ($this->email === 'admin@pscranker.com') {
            return true;
        }

        return $this->subscribed_until && $this->subscribed_until->isFuture();
    }

    /**
     * Get remaining days in prepaid subscription.
     */
    public function subscriptionDaysRemaining(): int
    {
        if ($this->email === 'admin@pscranker.com') {
            return 365;
        }

        if (!$this->subscribed_until || $this->subscribed_until->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->subscribed_until, false);
    }
}
