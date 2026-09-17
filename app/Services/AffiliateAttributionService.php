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

        $rate = (float) $affiliate->commission_rate;
        $commissionAmount = round(($courseAmount * $rate) / 100, 2);
        $totalAmount = round($commissionAmount + $bonus, 2);
        $periodMonth = now()->format('Y-m');

        $commission = AffiliateCommission::create([
            'affiliate_id' => $affiliate->id,
            'affiliate_lead_id' => $lead->id,
            'subscription_payment_id' => $payment?->id,
            'user_id' => $student->id,
            'course_amount' => $courseAmount,
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'bonus_amount' => $bonus,
            'total_amount' => $totalAmount,
            'period_month' => $periodMonth,
            'status' => 'pending',
            'admin_notes' => $adminNotes,
        ]);

        Log::info("Affiliate conversion recorded: Lead #{$lead->id} -> Affiliate #{$affiliate->id} (Anu/Promoter) for Student #{$student->id}. Commission: ₹{$commissionAmount}");

        return $commission;
    }

    /**
     * Link student account to lead upon registration if phone matches.
     */
    public function linkRegisteredStudent(User $student): ?AffiliateLead
    {
        if (!$student->phone) {
            return null;
        }

        $lead = $this->findActiveLeadByPhone($student->phone);
        if ($lead) {
            $lead->update([
                'converted_user_id' => $student->id,
            ]);
        }

        return $lead;
    }
}
