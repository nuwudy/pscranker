<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $table = 'subscription_payments';

    protected $fillable = [
        'user_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'currency',
        'duration_months',
        'rebate_percentage',
        'status',
        'payment_metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_months' => 'integer',
        'rebate_percentage' => 'decimal:2',
        'payment_metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
