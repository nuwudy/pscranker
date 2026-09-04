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
    public static function getPricingTiers(): array
    {
        $baseMonthlyFee = (float) self::get('course_base_monthly_fee', 299);
        $rebate2m = (float) self::get('rebate_2m', 10);
        $rebate3m = (float) self::get('rebate_3m', 15);
        $rebate6m = (float) self::get('rebate_6m', 25);
        $rebate12m = (float) self::get('rebate_12m', 40);

        $durations = [
            [
                'months' => 1,
                'name' => '1 Month Flex Pass',
                'name_malayalam' => '1 മാസത്തെ ഫ്ലെക്സ് പാസ്',
                'rebate_percent' => 0,
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => 'Starter Trial',
                'description' => 'Perfect for quick diagnostic check & testing your speed',
            ],
            [
                'months' => 2,
                'name' => '2 Months Rapid Revision',
                'name_malayalam' => '2 മാസത്തെ റാപ്പിഡ് റിവിഷൻ',
                'rebate_percent' => $rebate2m,
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => "Save {$rebate2m}%",
                'description' => 'Targeted syllabus revision before exam date',
            ],
            [
                'months' => 3,
                'name' => '3 Months Exam Sprint',
                'name_malayalam' => '3 മാസത്തെ എക്സാം സ്പ്രിന്റ്',
                'rebate_percent' => $rebate3m,
                'is_popular' => true,
                'is_best_value' => false,
                'badge' => '🔥 MOST POPULAR',
                'description' => 'Ideal 90-day mastery cycle for Kerala PSC notifications',
            ],
            [
                'months' => 6,
                'name' => '6 Months Semester Pass',
                'name_malayalam' => '6 മാസത്തെ റാങ്ക് അഷ്വേർഡ് പാസ്',
                'rebate_percent' => $rebate6m,
                'is_popular' => false,
                'is_best_value' => false,
                'badge' => "Save {$rebate6m}%",
                'description' => 'Complete coverage: GK, Malayalam, English & Maths',
            ],
            [
                'months' => 12,
                'name' => '1 Year All-Access Pass',
                'name_malayalam' => '1 വർഷത്തെ ഓൾ-ആക്സസ് പാസ്',
                'rebate_percent' => $rebate12m,
                'is_popular' => false,
                'is_best_value' => true,
                'badge' => '👑 BEST VALUE',
                'description' => 'Unrestricted access to every current & upcoming PSC unit',
            ],
        ];

        $tiers = [];
        foreach ($durations as $plan) {
            $months = $plan['months'];
            $rebate = $plan['rebate_percent'];
            $baseTotal = round($baseMonthlyFee * $months);
            $discountAmount = round(($baseTotal * $rebate) / 100);
            $finalPrice = max(1, $baseTotal - $discountAmount);
            $effectivePerMonth = round($finalPrice / $months);

            $tiers[] = [
                'months' => $months,
                'name' => $plan['name'],
                'name_malayalam' => $plan['name_malayalam'],
                'base_monthly_fee' => $baseMonthlyFee,
                'base_total' => $baseTotal,
                'rebate_percent' => $rebate,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'effective_per_month' => $effectivePerMonth,
                'is_popular' => $plan['is_popular'],
                'is_best_value' => $plan['is_best_value'],
                'badge' => $plan['badge'],
                'description' => $plan['description'],
            ];
        }

        return $tiers;
    }
}
