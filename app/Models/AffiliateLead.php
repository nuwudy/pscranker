<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AffiliateLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'candidate_name',
        'candidate_phone',
        'alternate_phone',
        'status',
        'notes',
        'converted_user_id',
        'converted_at',
        'valid_until',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function convertedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_user_id');
    }

    public function commission(): HasOne
    {
        return $this->hasOne(AffiliateCommission::class, 'affiliate_lead_id');
    }

    /**
     * Standardize any phone input down to pure 10 digits.
     * Handles formats: "+91 91234 56789", "09123456789", "91234-56789", "919123456789", "9123456789".
     */
    public static function normalizePhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        // Keep only digits
        $digits = preg_replace('/\D/', '', $phone);

        // If prefixed with 91 (India) and is 12 digits, take last 10
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        // If prefixed with 0 and is 11 digits, take last 10
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // If longer than 10 digits for any reason, grab the last 10 digits
        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        return $digits;
    }

    /**
     * Scope to find active leads (not expired, not converted).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'lead')
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }
}
