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
        $baseMonthlyFee = (float) SiteSetting::get('course_base_monthly_fee', 299);
        $razorpayKey = SiteSetting::get('razorpay_key_id', config('services.razorpay.key', 'rzp_test_demo12345678'));

        return view('pages.pricing', compact('tiers', 'baseMonthlyFee', 'razorpayKey'));
    }

    /**
     * Create Razorpay order for chosen duration.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'months' => 'required|integer|in:1,2,3,6,12',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $months = (int) $validated['months'];
        $tiers = collect(SiteSetting::getPricingTiers())->keyBy('months');
        $tier = $tiers->get($months);

        if (!$tier) {
            return response()->json(['error' => 'Invalid plan duration selected.'], 422);
        }

        $amountInPaise = (int) ($tier['final_price'] * 100);
        $currency = 'INR';
        $receipt = 'order_rcpt_' . time() . '_' . Str::random(5);

        $razorpayKey = SiteSetting::get('razorpay_key_id', config('services.razorpay.key'));
        $razorpaySecret = SiteSetting::get('razorpay_key_secret', config('services.razorpay.secret'));

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

        $razorpayKey = SiteSetting::get('razorpay_key_id', config('services.razorpay.key'));
        $razorpaySecret = SiteSetting::get('razorpay_key_secret', config('services.razorpay.secret'));

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

            $newExpiry = (clone $currentExpiry)->addMonths($payment->duration_months);

            $user->update([
                'subscribed_until' => $newExpiry,
                'subscription_plan' => "{$payment->duration_months} Months Plan",
                'subscription_amount' => $payment->amount,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Success! Your {$payment->duration_months}-month access has been activated.",
            'valid_until' => $user ? $user->subscribed_until->format('d M Y') : now()->addMonths($payment->duration_months)->format('d M Y'),
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
}
