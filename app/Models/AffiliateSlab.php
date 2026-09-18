<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateSlab extends Model
{
    use HasFactory;

    protected $table = 'affiliate_slabs';

    protected $fillable = [
        'slab_code',
        'month_period',
        'order',
        'min_target',
        'max_target',
        'basic_payout_percentage',
        'bonus_percentage',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'min_target' => 'decimal:2',
        'max_target' => 'decimal:2',
        'basic_payout_percentage' => 'decimal:2',
        'bonus_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Total payout percentage (Basic % + Bonus %).
     */
    public function getTotalPayoutPercentageAttribute(): float
    {
        return round((float) $this->basic_payout_percentage + (float) $this->bonus_percentage, 2);
    }

    /**
     * Human-friendly slab name (e.g. "Slab 1", "Slab 4").
     */
    public function getSlabNameAttribute(): string
    {
        return 'Slab ' . $this->order;
    }

    /**
     * Scope query to active slabs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query ordered sequentially.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Scope query for a specific month period (or default templates where month_period is null).
     */
    public function scopeForMonth($query, ?string $monthPeriod = null)
    {
        if ($monthPeriod) {
            // If month-specific slabs exist, use them; otherwise use null defaults
            return $query->where('month_period', $monthPeriod)
                ->orWhereNull('month_period');
        }

        return $query->whereNull('month_period');
    }

    /**
     * Formatted Target Range string (e.g. "₹1 - ₹10,000" or "₹150,001+").
     */
    public function getFormattedRangeAttribute(): string
    {
        if ($this->max_target === null) {
            return '₹' . number_format($this->min_target) . '+';
        }

        return '₹' . number_format($this->min_target) . ' - ₹' . number_format($this->max_target);
    }

    /**
     * Generate standard slab code e.g. PRSL-2609-001.
     */
    public static function generateCode(?string $monthPeriod = null, int $order = 1): string
    {
        $ym = $monthPeriod ? str_replace('-', '', substr($monthPeriod, 2)) : now()->format('ym');
        return sprintf('PRSL-%s-%03d', $ym, $order);
    }
}
