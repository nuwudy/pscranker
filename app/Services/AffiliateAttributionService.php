<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLead;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AffiliateAttributionService
{
    /**
     * Find active unexpired lead claimed by an active affiliate for the given phone.
     */
    public function findActiveLeadByPhone(string $rawPhone): ?AffiliateLead
    {
        $normalized = AffiliateLead::normalizePhone($rawPhone);
        if (empty($normalized) || strlen($normalized) < 10) {
            return null;
        }

        return AffiliateLead::where(function ($query) use ($normalized) {
                $query->where('candidate_phone', $normalized)
                      ->orWhere('alternate_phone', $normalized);
            })
            ->where('status', 'lead')
            ->where(function ($query) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
            })
            ->whereHas('affiliate', function ($query) {
                $query->where('status', 'active');
            })
            ->latest('id')
            ->first();
    }

    /**
     * Check if a prospect phone can be registered by an affiliate, or if it's already active.
     */
    public function checkPhoneAvailability(string $rawPhone, ?int $affiliateId = null): array
    {
        $normalized = AffiliateLead::normalizePhone($rawPhone);

        if (strlen($normalized) !== 10) {
            return [
                'allowed' => false,
                'message' => 'Please enter a valid 10-digit mobile number.',
            ];
        }

        // Check if an existing student is already active/subscribed
        $existingStudent = User::where('phone', $normalized)->first();
        if ($existingStudent && $existingStudent->isSubscribed()) {
            return [
                'allowed' => false,
                'message' => 'This candidate is already an active subscribed student on PSCRanker.',
            ];
        }

        // Check if another promoter has an active unexpired lead on this phone
        $existingLead = AffiliateLead::where('candidate_phone', $normalized)
            ->where('status', 'lead')
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->when($affiliateId, function ($q) use ($affiliateId) {
                $q->where('affiliate_id', '!=', $affiliateId);
            })
            ->first();

        if ($existingLead) {
            return [
                'allowed' => false,
                'message' => 'This phone number is currently being followed up by another promoter.',
            ];
        }

        return [
            'allowed' => true,
            'normalized_phone' => $normalized,
        ];
    }

    /**
     * Attribute a paid course enrolment to the affiliate who pitched the candidate.
     */
    public function recordConversion(
        User $student,
        ?SubscriptionPayment $payment,
        float $courseAmount,
        float $bonus = 0.0,
        ?string $adminNotes = null
    ): ?AffiliateCommission {
        if (!$student->phone && (!$payment || empty($payment->payment_metadata['customer_phone']))) {
            return null;
        }

        $phone = $student->phone ?? ($payment->payment_metadata['customer_phone'] ?? null);
        $lead = $this->findActiveLeadByPhone($phone);

        if (!$lead) {
            // Check if student already converted earlier under this lead
            $lead = AffiliateLead::where('candidate_phone', AffiliateLead::normalizePhone($phone))
                ->where('status', 'converted')
                ->where('converted_user_id', $student->id)
                ->whereHas('affiliate', fn($q) => $q->where('status', 'active'))
                ->latest('id')
                ->first();
        }

        if (!$lead) {
            // Check if student was previously linked via referral link or earlier conversion
            $lead = AffiliateLead::where('converted_user_id', $student->id)
                ->whereHas('affiliate', fn($q) => $q->where('status', 'active'))
                ->latest('id')
                ->first();
        }

        // Check if referral link was stored in session or cookie
        if (!$lead) {
            $refCode = session('affiliate_ref') ?? request()->cookie('affiliate_ref');
            if ($refCode) {
                $refAffiliate = Affiliate::where('affiliate_code', strtoupper(trim($refCode)))
                    ->where('status', 'active')
                    ->first();

                if ($refAffiliate) {
                    $lead = AffiliateLead::create([
                        'affiliate_id' => $refAffiliate->id,
                        'candidate_name' => $student->name,
                        'candidate_phone' => $student->phone ? AffiliateLead::normalizePhone($student->phone) : 'Referral Link',
                        'status' => 'converted',
                        'source' => 'referral_link',
                        'notes' => 'Enrolled via Affiliate Referral Link',
                        'converted_user_id' => $student->id,
                        'converted_at' => now(),
                        'valid_until' => now()->addDays(60),
                    ]);
                }
            }
        }

        if (!$lead) {
            return null;
        }

        $affiliate = $lead->affiliate;
        if (!$affiliate || !$affiliate->isActive()) {
            return null;
        }

        // Mark lead as converted
        $lead->update([
            'status' => 'converted',
            'converted_user_id' => $student->id,
            'converted_at' => $lead->converted_at ?: now(),
        ]);

        $periodMonth = now()->format('Y-m');

        // Check if Slab System is active
        $slabService = app(AffiliateSlabService::class);
        $slabs = $slabService->getSlabsForMonth($periodMonth);

        if ($slabs->isNotEmpty()) {
            // Calculate total cumulative sales including this conversion
            $currentMonthSales = (float) AffiliateCommission::where('affiliate_id', $affiliate->id)
                ->where('period_month', $periodMonth)
                ->sum('course_amount') + $courseAmount;

            $matchedSlab = $slabService->getMatchingSlab($currentMonthSales, $periodMonth);

            $basicRate = $matchedSlab ? (float) $matchedSlab->basic_payout_percentage : (float) $affiliate->commission_rate;
            $bonusRate = $matchedSlab ? (float) $matchedSlab->bonus_percentage : 0.00;
            $rate = round($basicRate + $bonusRate, 2);

            $commissionAmount = round(($courseAmount * $basicRate) / 100, 2);
            $slabBonusAmount = round(($courseAmount * $bonusRate) / 100, 2);
            $totalBonus = round($slabBonusAmount + $bonus, 2);
            $totalAmount = round($commissionAmount + $totalBonus, 2);
        } else {
            $rate = (float) $affiliate->commission_rate;
            $commissionAmount = round(($courseAmount * $rate) / 100, 2);
            $totalBonus = $bonus;
            $totalAmount = round($commissionAmount + $totalBonus, 2);
        }

        $commission = AffiliateCommission::create([
            'affiliate_id' => $affiliate->id,
            'affiliate_lead_id' => $lead->id,
            'subscription_payment_id' => $payment?->id,
            'user_id' => $student->id,
            'course_amount' => $courseAmount,
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'bonus_amount' => $totalBonus,
            'total_amount' => $totalAmount,
            'period_month' => $periodMonth,
            'status' => 'pending',
            'admin_notes' => $adminNotes ?? ($lead->source === 'referral_link' ? 'Converted via Referral Link' : null),
        ]);

        // Automatically sync all pending commissions in this month with the achieved slab rate
        if ($slabs->isNotEmpty()) {
            $slabService->recalculateMonthlyCommissions($periodMonth);
        }

        Log::info("Affiliate conversion recorded: Lead #{$lead->id} -> Affiliate #{$affiliate->id} for Student #{$student->id}. Commission: ₹{$commissionAmount}");

        return $commission;
    }

    /**
     * Link student account to lead upon registration if phone matches or referral link used.
     */
    public function linkRegisteredStudent(User $student): ?AffiliateLead
    {
        $lead = null;

        if ($student->phone) {
            $lead = $this->findActiveLeadByPhone($student->phone);
        }

        if ($lead) {
            $lead->update([
                'converted_user_id' => $student->id,
            ]);
            return $lead;
        }

        // Check if referral link was stored in session or cookie
        $refCode = session('affiliate_ref') ?? request()->cookie('affiliate_ref');
        if ($refCode) {
            $affiliate = Affiliate::where('affiliate_code', strtoupper(trim($refCode)))
                ->where('status', 'active')
                ->first();

            if ($affiliate) {
                $lead = AffiliateLead::create([
                    'affiliate_id' => $affiliate->id,
                    'candidate_name' => $student->name,
                    'candidate_phone' => $student->phone ? AffiliateLead::normalizePhone($student->phone) : 'Referral Link',
                    'status' => 'lead',
                    'source' => 'referral_link',
                    'notes' => 'Registered via Affiliate Referral Link',
                    'converted_user_id' => $student->id,
                    'valid_until' => now()->addDays(60),
                ]);
            }
        }

        return $lead;
    }

    /**
     * Attribute a subscription payment directly.
     */
    public function attributePayment(SubscriptionPayment $payment): ?AffiliateCommission
    {
        $user = $payment->user ?? User::find($payment->user_id);
        if (!$user) {
            return null;
        }

        return $this->recordConversion($user, $payment, (float) $payment->amount);
    }
}
