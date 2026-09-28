<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PricingController extends Controller
{
    /**
     * Display the dynamic pricing page with interactive duration dropdown.
     */
    public function index()
    {
        $tiers = SiteSetting::getPricingTiers();
        $dailyBaseFee = (float) SiteSetting::get('course_base_daily_fee', null);
        if ($dailyBaseFee === null || $dailyBaseFee <= 0) {
            $monthlyFee = (float) SiteSetting::get('course_base_monthly_fee', 300);
            $dailyBaseFee = $monthlyFee > 0 ? round($monthlyFee / 30, 2) : 10.00;
        }
        $baseMonthlyFee = (float) round($dailyBaseFee * 30);
        $razorpayKey = SiteSetting::get('razorpay_key_id') ?: (config('services.razorpay.key') ?: 'rzp_test_demo12345678');

        return view('pages.pricing', compact('tiers', 'dailyBaseFee', 'baseMonthlyFee', 'razorpayKey'));
    }

    /**
     * Create Razorpay order for chosen duration.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'days' => 'nullable|integer',
            'months' => 'nullable|integer',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $tiers = SiteSetting::getPricingTiers();
        $tier = null;

        if (!empty($validated['days'])) {
            $days = (int) $validated['days'];
            $tier = collect($tiers)->firstWhere('days', $days);
        } elseif (!empty($validated['months'])) {
            $months = (int) $validated['months'];
            $tier = collect($tiers)->firstWhere('months', $months);
            if (!$tier && $months === 1) {
                $tier = collect($tiers)->firstWhere('days', 30);
            }
        } else {
            $tier = collect($tiers)->firstWhere('is_popular', true) ?: $tiers[0];
        }

        if (!$tier) {
            return response()->json(['error' => 'Invalid plan duration selected.'], 422);
        }

        $days = (int) $tier['days'];
        $months = (int) ($tier['months'] ?? max(1, round($days / 30)));

        $amountInPaise = (int) ($tier['final_price'] * 100);
        $currency = 'INR';
        $receipt = 'order_rcpt_' . time() . '_' . Str::random(5);

        $razorpayKey = SiteSetting::get('razorpay_key_id') ?: config('services.razorpay.key');
        $razorpaySecret = SiteSetting::get('razorpay_key_secret') ?: config('services.razorpay.secret');

        $razorpayOrderId = null;

        // If merchant has configured real or test Razorpay credentials
        if ($razorpayKey && $razorpaySecret && !str_starts_with($razorpayKey, 'rzp_test_demo')) {
            try {
                $response = Http::withBasicAuth($razorpayKey, $razorpaySecret)
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => $amountInPaise,
                        'currency' => $currency,
                        'receipt' => $receipt,
                        'notes' => [
                            'plan' => $tier['name'],
                            'days' => $days,
                            'months' => $months,
                            'customer_email' => $validated['email'] ?? (Auth::user()?->email ?? ''),
                        ],
                    ]);

                if ($response->successful()) {
                    $razorpayOrderId = $response->json('id');
                } else {
                    Log::error('Razorpay Order API Failed: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Razorpay Order Exception: ' . $e->getMessage());
            }
        }

        // Fallback for seamless demo / test environment
        if (!$razorpayOrderId) {
            $razorpayOrderId = 'order_psc_' . uniqid() . '_' . Str::random(6);
        }

        // Record pending order
        $payment = SubscriptionPayment::create([
            'user_id' => Auth::id(),
            'razorpay_order_id' => $razorpayOrderId,
            'amount' => $tier['final_price'],
            'currency' => $currency,
            'duration_months' => $months,
            'duration_days' => $days,
            'rebate_percentage' => $tier['rebate_percent'],
            'status' => 'created',
            'payment_metadata' => [
                'plan_name' => $tier['name'],
                'receipt' => $receipt,
                'customer_name' => $validated['name'] ?? (Auth::user()?->name ?? 'Aspirant'),
                'customer_email' => $validated['email'] ?? (Auth::user()?->email ?? 'candidate@pscranker.com'),
                'customer_phone' => $validated['phone'] ?? '9876543210',
            ],
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $razorpayOrderId,
            'amount' => $amountInPaise,
            'amount_inr' => $tier['final_price'],
            'currency' => $currency,
            'plan_name' => $tier['name'],
            'days' => $days,
            'months' => $months,
            'key' => $razorpayKey ?: 'rzp_test_demo12345678',
            'is_mock' => !$razorpayKey || str_starts_with($razorpayKey, 'rzp_test_demo'),
        ]);
    }

    /**
     * Verify payment and activate user subscription.
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        $payment = SubscriptionPayment::where('razorpay_order_id', $validated['razorpay_order_id'])->first();

        if (!$payment) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        $razorpayKey = SiteSetting::get('razorpay_key_id') ?: config('services.razorpay.key');
        $razorpaySecret = SiteSetting::get('razorpay_key_secret') ?: config('services.razorpay.secret');

        // Verify Razorpay signature if live secret is available
        if ($razorpaySecret && !empty($validated['razorpay_signature'])) {
            $expectedSignature = hash_hmac('sha256', $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'], $razorpaySecret);
            if (!hash_equals($expectedSignature, $validated['razorpay_signature'])) {
                $payment->update(['status' => 'failed']);
                return response()->json(['error' => 'Payment signature verification failed.'], 400);
            }
        }

        // Mark payment as paid
        $payment->update([
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'] ?? 'verified_signature',
            'status' => 'paid',
        ]);

        // Grant prepaid subscription time to user
        $user = $payment->user ?? Auth::user();
        if ($user) {
            $currentExpiry = ($user->subscribed_until && $user->subscribed_until->isFuture())
                ? $user->subscribed_until
                : now();

            $daysToAdd = $payment->duration_days ?: ($payment->duration_months * 30);
            $newExpiry = (clone $currentExpiry)->addDays($daysToAdd);

            $user->update([
                'subscribed_until' => $newExpiry,
                'subscription_plan' => $payment->payment_metadata['plan_name'] ?? "{$daysToAdd} Days Plan",
                'subscription_amount' => $payment->amount,
            ]);

            // Automatically check and attribute affiliate commission by phone number
            try {
                app(\App\Services\AffiliateAttributionService::class)->recordConversion(
                    student: $user,
                    payment: $payment,
                    courseAmount: (float) $payment->amount
                );
            } catch (\Throwable $e) {
                Log::warning('Affiliate commission recording exception: ' . $e->getMessage());
            }
        }

        $durationLabel = $payment->duration_days ? ($payment->duration_days . ' days') : ($payment->duration_months . ' months');

        return response()->json([
            'success' => true,
            'message' => "Success! Your {$durationLabel} access has been activated.",
            'valid_until' => $user ? $user->subscribed_until->format('d M Y') : now()->addDays($payment->duration_days ?: 30)->format('d M Y'),
        ]);
    }

    /**
     * Mandatory Razorpay Compliance Page: Terms and Conditions
     */
    public function terms()
    {
        return view('pages.legal.terms');
    }

    /**
     * Mandatory Razorpay Compliance Page: Privacy Policy
     */
    public function privacy()
    {
        return view('pages.legal.privacy');
    }

    /**
     * Mandatory Razorpay Compliance Page: Cancellation & Refund Policy
     */
    public function refundPolicy()
    {
        return view('pages.legal.refund');
    }

    /**
     * Mandatory Razorpay Compliance Page: Contact Us & Grievance Support
     */
    public function contact()
    {
        return view('pages.legal.contact');
    }

    /**
     * Mandatory Razorpay Compliance Page: Shipping & Delivery Policy
     */
    public function shippingPolicy()
    {
        return view('pages.legal.shipping');
    }

    /**
     * Mandatory Razorpay Compliance Page: About Us
     */
    public function about()
    {
        return view('pages.legal.about');
    }
}
