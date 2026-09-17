<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'affiliate_code',
        'status',
        'commission_rate',
        'payout_method',
        'payout_details',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'payout_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(AffiliateLead::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function totalEarned(): float
    {
        return (float) $this->commissions()->sum('total_amount');
    }

    public function totalDisbursed(): float
    {
        return (float) $this->commissions()->where('status', 'disbursed')->sum('total_amount');
    }

    public function pendingPayout(): float
    {
        return (float) $this->commissions()->where('status', 'pending')->sum('total_amount');
    }

    public function convertedLeadsCount(): int
    {
        return $this->leads()->where('status', 'converted')->count();
    }

    public function activeLeadsCount(): int
    {
        return $this->leads()->where('status', 'lead')->count();
    }

    public function getUpiIdAttribute(): ?string
    {
        return $this->payout_details['upi_id'] ?? null;
    }
}
