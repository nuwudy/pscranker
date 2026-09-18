<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateSlab;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AffiliateSlabService
{
    /**
     * Get active slabs for a specific month period (or default templates).
     */
    public function getSlabsForMonth(?string $monthPeriod = null): Collection
    {
        $this->ensureDefaultSlabsExist();

        $monthPeriod = $monthPeriod ?? now()->format('Y-m');

        // Check if there are month-specific customized slabs
        $monthSpecific = AffiliateSlab::where('month_period', $monthPeriod)
            ->active()
            ->ordered()
            ->get();

        if ($monthSpecific->isNotEmpty()) {
            return $monthSpecific;
        }

        // Fallback to default platform template slabs
        return AffiliateSlab::whereNull('month_period')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Ensure default 10 template slabs exist in database if empty.
     */
    public function ensureDefaultSlabsExist(): void
    {
        try {
            if (AffiliateSlab::count() === 0) {
                $defaultSlabs = [
                    ['order' => 1, 'min_target' => 1.00, 'max_target' => 10000.00, 'basic' => 10.00, 'bonus' => 0.00],
                    ['order' => 2, 'min_target' => 10001.00, 'max_target' => 20000.00, 'basic' => 10.00, 'bonus' => 5.00],
                    ['order' => 3, 'min_target' => 20001.00, 'max_target' => 30000.00, 'basic' => 10.00, 'bonus' => 7.00],
                    ['order' => 4, 'min_target' => 30001.00, 'max_target' => 40000.00, 'basic' => 10.00, 'bonus' => 9.00],
                    ['order' => 5, 'min_target' => 40001.00, 'max_target' => 50000.00, 'basic' => 10.00, 'bonus' => 11.00],
                    ['order' => 6, 'min_target' => 50001.00, 'max_target' => 75000.00, 'basic' => 10.00, 'bonus' => 13.00],
                    ['order' => 7, 'min_target' => 75001.00, 'max_target' => 100000.00, 'basic' => 10.00, 'bonus' => 15.00],
                    ['order' => 8, 'min_target' => 100001.00, 'max_target' => 125000.00, 'basic' => 10.00, 'bonus' => 17.00],
                    ['order' => 9, 'min_target' => 125001.00, 'max_target' => 150000.00, 'basic' => 10.00, 'bonus' => 19.00],
                    ['order' => 10, 'min_target' => 150001.00, 'max_target' => null, 'basic' => 10.00, 'bonus' => 21.00],
                ];

                $currentMonth = now()->format('ym');

                foreach ($defaultSlabs as $slab) {
                    $code = sprintf('PRSL-%s-%03d', $currentMonth, $slab['order']);
                    AffiliateSlab::create([
                        'slab_code' => $code,
                        'month_period' => null,
                        'order' => $slab['order'],
                        'min_target' => $slab['min_target'],
                        'max_target' => $slab['max_target'],
                        'basic_payout_percentage' => $slab['basic'],
                        'bonus_percentage' => $slab['bonus'],
                        'is_active' => true,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Table might not exist yet before migration
        }
    }

    /**
     * Find the matching slab for a given sales volume.
     */
    public function getMatchingSlab(float $salesValue, ?string $monthPeriod = null): ?AffiliateSlab
    {
        $slabs = $this->getSlabsForMonth($monthPeriod);

        if ($slabs->isEmpty()) {
            return null;
        }

        // Loop through slabs to find where salesValue falls within [min_target, max_target]
        foreach ($slabs as $slab) {
            $min = (float) $slab->min_target;
            $max = $slab->max_target !== null ? (float) $slab->max_target : null;

            if ($max === null) {
                // Top open-ended bracket
                if ($salesValue >= $min) {
                    return $slab;
                }
            } else {
                if ($salesValue >= $min && $salesValue <= $max) {
                    return $slab;
                }
            }
        }

        // If sales are 0 or less than Tier 1 min, return Tier 1
        return $slabs->first();
    }

    /**
     * Alias for getMatchingSlab based on sales volume.
     */
    public function getSlabForSales(float $salesValue, ?string $monthPeriod = null): ?AffiliateSlab
    {
        return $this->getMatchingSlab($salesValue, $monthPeriod);
    }

    /**
     * Calculate basic, bonus, and total payout amounts for a given sales amount and slab.
     */
    public function calculatePayout(float $salesValue, ?AffiliateSlab $slab): array
    {
        $basicRate = $slab ? (float) $slab->basic_payout_percentage : 10.00;
        $bonusRate = $slab ? (float) $slab->bonus_percentage : 0.00;
        $totalRate = $basicRate + $bonusRate;

        $basicAmount = round(($salesValue * $basicRate) / 100, 2);
        $bonusAmount = round(($salesValue * $bonusRate) / 100, 2);
        $totalAmount = round(($salesValue * $totalRate) / 100, 2);

        return [
            'basic_percentage' => $basicRate,
            'bonus_percentage' => $bonusRate,
            'total_percentage' => $totalRate,
            'basic_amount' => $basicAmount,
            'bonus_amount' => $bonusAmount,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Calculate comprehensive monthly performance metrics for an affiliate.
     */
    public function getAffiliateMonthlyPerformance(Affiliate $affiliate, ?string $monthPeriod = null): array
    {
        $monthPeriod = $monthPeriod ?? now()->format('Y-m');
        $slabs = $this->getSlabsForMonth($monthPeriod);

        // Sum of all course payments attributed to this affiliate in this month
        $salesValue = (float) AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('period_month', $monthPeriod)
            ->sum('course_amount');

        $currentSlab = $this->getMatchingSlab($salesValue, $monthPeriod);

        $basicPercentage = $currentSlab ? (float) $currentSlab->basic_payout_percentage : (float) $affiliate->commission_rate;
        $bonusPercentage = $currentSlab ? (float) $currentSlab->bonus_percentage : 0.00;
        $totalPercentage = round($basicPercentage + $bonusPercentage, 2);

        $basicPayoutAmount = round(($salesValue * $basicPercentage) / 100, 2);
        $bonusPayoutAmount = round(($salesValue * $bonusPercentage) / 100, 2);

        // Total earnings calculation = (Sales * Total Payout %)
        $earnings = round(($salesValue * $totalPercentage) / 100, 2);

        // Date calculations for velocity and countdown
        $monthDate = Carbon::parse($monthPeriod . '-01');
        $isCurrentMonth = $monthDate->isCurrentMonth();
        $daysInMonth = $monthDate->daysInMonth;

        if ($isCurrentMonth) {
            $daysElapsed = max(1, now()->day);
            $daysLeft = max(0, $daysInMonth - now()->day);
        } else {
            $daysElapsed = $daysInMonth;
            $daysLeft = 0;
        }

        $avgSalesPerDay = round($salesValue / $daysElapsed, 2);
        $avgEarningsPerDay = round($earnings / $daysElapsed, 2);

        // Next Slab Target calculation
        $nextSlab = null;
        $salesNeededForNextSlab = 0.0;
        $projectedNextEarnings = 0.0;
        $earningsBoost = 0.0;

        if ($currentSlab) {
            $nextSlab = $slabs->firstWhere('order', $currentSlab->order + 1);

            if ($nextSlab) {
                $salesNeededForNextSlab = max(0.0, (float) $nextSlab->min_target - $salesValue);
                $projectedNextEarnings = round(((float) $nextSlab->min_target * $nextSlab->total_payout_percentage) / 100, 2);
                $earningsBoost = max(0.0, $projectedNextEarnings - $earnings);
            }
        }

        // Decorate slabs collection with is_current flag and dynamic code
        $formattedSlabs = $slabs->map(function ($slab) use ($currentSlab, $monthPeriod) {
            $slab->is_current = $currentSlab && $currentSlab->id === $slab->id;
            $slab->display_code = $slab->slab_code ?: AffiliateSlab::generateCode($monthPeriod, $slab->order);
            return $slab;
        });

        return [
            'month_period' => $monthPeriod,
            'month_name' => $monthDate->format('F Y'), // e.g. "September 2026"
            'sales_value' => $salesValue,
            'earnings' => $earnings,
            'total_earnings' => $earnings,
            'basic_payout_amount' => $basicPayoutAmount,
            'bonus_amount' => $bonusPayoutAmount,
            'current_slab' => $currentSlab,
            'current_slab_code' => $currentSlab ? ($currentSlab->slab_code ?: AffiliateSlab::generateCode($monthPeriod, $currentSlab->order)) : 'None',
            'basic_percentage' => $basicPercentage,
            'bonus_percentage' => $bonusPercentage,
            'total_percentage' => $totalPercentage,
            'avg_sales_per_day' => $avgSalesPerDay,
            'avg_earnings_per_day' => $avgEarningsPerDay,
            'average_sales_per_day' => $avgSalesPerDay,
            'average_earnings_per_day' => $avgEarningsPerDay,
            'days_in_month' => $daysInMonth,
            'days_passed' => $daysElapsed,
            'days_left' => $daysLeft,
            'days_remaining' => $daysLeft,
            'next_slab' => $nextSlab,
            'sales_needed_for_next_slab' => $salesNeededForNextSlab,
            'amount_needed_for_next_slab' => $salesNeededForNextSlab,
            'projected_next_earnings' => $projectedNextEarnings,
            'earnings_boost' => $earningsBoost,
            'potential_additional_earnings' => $earningsBoost,
            'slabs' => $formattedSlabs,
            'all_slabs' => $formattedSlabs,
        ];
    }

    /**
     * Recalculate and update commissions for all affiliates in a given month based on active slabs.
     */
    public function recalculateMonthlyCommissions(?string $monthPeriod = null): array
    {
        $monthPeriod = $monthPeriod ?? now()->format('Y-m');

        $affiliateIds = AffiliateCommission::where('period_month', $monthPeriod)
            ->where('status', '!=', 'disbursed') // Don't alter already disbursed payouts
            ->distinct()
            ->pluck('affiliate_id');

        $updatedAffiliatesCount = 0;
        $totalAdjustedCommission = 0.0;

        foreach ($affiliateIds as $affiliateId) {
            $affiliate = Affiliate::find($affiliateId);
            if (!$affiliate) continue;

            $performance = $this->getAffiliateMonthlyPerformance($affiliate, $monthPeriod);
            $totalPercentage = $performance['total_percentage'];
            $basicPercentage = $performance['basic_percentage'];
            $bonusPercentage = $performance['bonus_percentage'];

            // Update pending commissions for this month proportionally
            $commissions = AffiliateCommission::where('affiliate_id', $affiliate->id)
                ->where('period_month', $monthPeriod)
                ->where('status', '!=', 'disbursed')
                ->get();

            foreach ($commissions as $comm) {
                $commAmount = round(($comm->course_amount * $basicPercentage) / 100, 2);
                $bonusAmount = round(($comm->course_amount * $bonusPercentage) / 100, 2);
                $totalAmount = round($commAmount + $bonusAmount, 2);

                $comm->update([
                    'commission_rate' => $totalPercentage,
                    'commission_amount' => $commAmount,
                    'bonus_amount' => $bonusAmount,
                    'total_amount' => $totalAmount,
                ]);

                $totalAdjustedCommission += $totalAmount;
            }

            $updatedAffiliatesCount++;
        }

        return [
            'month_period' => $monthPeriod,
            'affiliates_updated' => $updatedAffiliatesCount,
            'total_adjusted_payout' => $totalAdjustedCommission,
        ];
    }
}
