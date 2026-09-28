<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting by key with optional fallback.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting && $setting->value !== null && $setting->value !== '' ? $setting->value : $default;
        });
    }

    /**
     * Set a setting by key and clear cache.
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("site_setting_{$key}");
    }

    /**
     * Generate the complete progressive rebate pricing schedule.
     * Calculated dynamically from the admin-configured base monthly fee.
     *
     * @return array<int, array<string, mixed>>
     */
    /**
     * Generate the complete progressive duration pricing schedule.
     * Calculated dynamically from the admin-configured baseline daily rate (e.g. ₹10 or ₹5 per day).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getPricingTiers(): array
    {
        $dailyRate = (float) self::get('course_base_daily_fee', null);
        if ($dailyRate === null || $dailyRate <= 0) {
            $monthlyFee = (float) self::get('course_base_monthly_fee', 300);
            $dailyRate = $monthlyFee > 0 ? round($monthlyFee / 30, 2) : 10.00;
        }

        $rebate1w = (float) self::get('rebate_1w', 14);
        $rebate1m = (float) self::get('rebate_1m', 25);
        $rebate3m = (float) self::get('rebate_3m', 33);
        $rebate6m = (float) self::get('rebate_6m', 40);
        $rebate1y = (float) self::get('rebate_1y', 45);

        $definitions = [
            [
                'days' => 1,
                'months' => 1,
                'code' => '1d',
                'name' => '1 Day Flex Pass',
                'name_malayalam' => '1 ദിവസത്തെ ഫ്ലെക്സ് പാസ്',
                'rebate_percent' => 0,
                'color' => '#EF4444', // Red
                'bg_class' => 'bg-red-500',
                'border_class' => 'border-red-500',
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => 'Instant Starter',
                'badge_malayalam' => 'തുടക്കക്കാർക്കായി',
                'description' => 'Single-day pass to drill all 4-phase sessions & simulate authentic OMR',
            ],
            [
                'days' => 7,
                'months' => 1,
                'code' => '1w',
                'name' => '1 Week Crash Pass',
                'name_malayalam' => '1 ആഴ്ചത്തെ ക്രാഷ് പാസ്',
                'rebate_percent' => $rebate1w,
                'color' => '#0052FF', // Blue
                'bg_class' => 'bg-[#0052FF]',
                'border_class' => 'border-[#0052FF]',
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => "Save {$rebate1w}%",
                'badge_malayalam' => "{$rebate1w}% ലാഭം",
                'description' => '7 days of uninterrupted practice for targeted syllabus sprints',
            ],
            [
                'days' => 30,
                'months' => 1,
                'code' => '1m',
                'name' => '1 Month Regular Pass',
                'name_malayalam' => '1 മാസത്തെ റഗുലർ പാസ്',
                'rebate_percent' => $rebate1m,
                'color' => '#C89D66', // Warm Tan / Gold
                'bg_class' => 'bg-[#C89D66]',
                'border_class' => 'border-[#C89D66]',
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => "Save {$rebate1m}%",
                'badge_malayalam' => "{$rebate1m}% ലാഭം",
                'description' => '30-day foundational rank-building cycle across all subjects',
            ],
            [
                'days' => 90,
                'months' => 3,
                'code' => '3m',
                'name' => '3 Months Exam Sprint',
                'name_malayalam' => '3 മാസത്തെ എക്സാം സ്പ്രിന്റ്',
                'rebate_percent' => $rebate3m,
                'color' => '#FF6B00', // Orange
                'bg_class' => 'bg-[#FF6B00]',
                'border_class' => 'border-[#FF6B00]',
                'is_popular' => true,
                'is_best_value' => false,
                'badge' => '🔥 MOST POPULAR',
                'badge_malayalam' => '🔥 കൂടുതൽ പേർ തിരഞ്ഞെടുക്കുന്നത്',
                'description' => 'Ideal 90-day mastery cycle for Kerala PSC notifications',
            ],
            [
                'days' => 180,
                'months' => 6,
                'code' => '6m',
                'name' => '6 Months Semester Pass',
                'name_malayalam' => '6 മാസത്തെ സെമസ്റ്റർ പാസ്',
                'rebate_percent' => $rebate6m,
                'color' => '#00C853', // Green
                'bg_class' => 'bg-[#00C853]',
                'border_class' => 'border-[#00C853]',
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => "Save {$rebate6m}%",
                'badge_malayalam' => "{$rebate6m}% ലാഭം",
                'description' => 'Comprehensive syllabus mastery: GK, Malayalam, English & Maths',
            ],
            [
                'days' => 365,
                'months' => 12,
                'code' => '1y',
                'name' => '1 Year All-Access Pass',
                'name_malayalam' => '1 വർഷത്തെ ഓൾ-ആക്സസ് പാസ്',
                'rebate_percent' => $rebate1y,
                'color' => '#FF4081', // Pink / Magenta
                'bg_class' => 'bg-[#FF4081]',
                'border_class' => 'border-[#FF4081]',
                'is_popular' => false,
                'is_best_value' => true,
                'badge' => '👑 BEST VALUE',
                'badge_malayalam' => '👑 മികച്ച മൂല്യം',
                'description' => 'Unrestricted access to every current & upcoming PSC unit',
            ],
        ];

        $tiers = [];
        foreach ($definitions as $plan) {
            $days = $plan['days'];
            $rebate = $plan['rebate_percent'];
            $linearTotal = (float) round($dailyRate * $days);

            if ($rebate > 0) {
                $rawPrice = $linearTotal * (1 - ($rebate / 100));
                // Psychological price rounding: for 365 days, round to ending 99 (e.g. 1999 or 999)
                if ($days === 365) {
                    $finalPrice = (float) (round($rawPrice / 100) * 100 - 1);
                } elseif ($days === 90) {
                    $finalPrice = (float) (round($rawPrice / 10) * 10);
                } else {
                    $finalPrice = (float) round($rawPrice);
                }
            } else {
                $finalPrice = (float) $linearTotal;
            }

            $finalPrice = max(1.0, $finalPrice);
            $discountAmount = max(0.0, (float) ($linearTotal - $finalPrice));
            $perDayCost = round($finalPrice / $days, 2);
            $effectivePerMonth = round(($finalPrice / $days) * 30, 2);

            $tiers[] = [
                'days' => $days,
                'months' => $plan['months'],
                'code' => $plan['code'],
                'name' => $plan['name'],
                'name_malayalam' => $plan['name_malayalam'],
                'daily_base_rate' => $dailyRate,
                'base_monthly_fee' => (float) round($dailyRate * 30),
                'base_total' => $linearTotal,
                'linear_total' => $linearTotal,
                'rebate_percent' => $rebate,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'per_day_cost' => $perDayCost,
                'effective_per_month' => $effectivePerMonth,
                'is_popular' => $plan['is_popular'],
                'is_best_value' => $plan['is_best_value'],
                'badge' => $plan['badge'],
                'badge_malayalam' => $plan['badge_malayalam'],
                'color' => $plan['color'],
                'bg_class' => $plan['bg_class'],
                'border_class' => $plan['border_class'],
                'description' => $plan['description'],
            ];
        }

        return $tiers;
    }
}
