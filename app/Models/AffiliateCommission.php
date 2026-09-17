<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'affiliate_lead_id',
        'subscription_payment_id',
        'user_id',
        'course_amount',
        'commission_rate',
        'commission_amount',
        'bonus_amount',
        'total_amount',
        'period_month',
        'status',
        'disbursed_at',
        'payout_reference',
        'admin_notes',
    ];

    protected $casts = [
        'course_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'disbursed_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AffiliateLead::class, 'affiliate_lead_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPayment::class, 'subscription_payment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }

    public function scopeForPeriod($query, string $periodMonth)
    {
        return $query->where('period_month', $periodMonth);
    }
}
