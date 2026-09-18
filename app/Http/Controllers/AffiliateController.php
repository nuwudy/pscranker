<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateLead;
use App\Models\User;
use App\Services\AffiliateAttributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    protected AffiliateAttributionService $attributionService;

    public function __construct(AffiliateAttributionService $attributionService)
    {
        $this->attributionService = $attributionService;
    }

    /**
     * Show the public "Become a Partner / Affiliate" onboarding page.
     */
    public function showJoinForm()
    {
        if (Auth::check() && Auth::user()->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }

        return view('pages.affiliate.join');
    }

    /**
     * Handle affiliate registration / application submission.
     */
    public function submitJoin(Request $request)
    {
        $cleanPhone = AffiliateLead::normalizePhone((string) $request->input('phone'));
        $request->merge(['phone' => $cleanPhone]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        // If not logged in, require password
        if (!Auth::check()) {
            $rules['password'] = ['required', 'string', 'min:6'];
        }

        $validated = $request->validate($rules, [
            'phone.regex' => 'Please enter a valid 10-digit mobile number.',
        ]);

        $user = Auth::user();

        if (!$user) {
            // Check if user with phone or email already exists
            $user = User::where('phone', $validated['phone'])
                ->orWhere('email', strtolower($validated['email']))
                ->first();

            if ($user) {
                // If exists and has affiliate account
                if ($user->affiliate) {
                    return redirect()->route('login')->with('info', 'You already have an affiliate partner account. Please log in.');
                }
            } else {
                // Create user
                $user = User::create([
                    'name' => trim($validated['name']),
                    'phone' => $validated['phone'],
                    'email' => strtolower(trim($validated['email'])),
                    'password' => Hash::make($validated['password']),
                ]);
            }

            Auth::login($user, true);
        }

        // Payout details structure
        $payoutDetails = [
            'upi_id' => trim($validated['upi_id'] ?? ''),
            'bank_name' => trim($validated['bank_name'] ?? ''),
            'account_holder' => trim($validated['account_holder'] ?? ''),
            'account_number' => trim($validated['account_number'] ?? ''),
            'ifsc_code' => strtoupper(trim($validated['ifsc_code'] ?? '')),
        ];
        $preferredMethod = !empty($payoutDetails['account_number']) ? 'bank_transfer' : 'upi';

        // Check if affiliate record already exists
        $affiliate = $user->affiliate;

        if (!$affiliate) {
            $affiliateCode = 'PSC-' . strtoupper(Str::random(6));

            $affiliate = Affiliate::create([
                'user_id' => $user->id,
                'affiliate_code' => $affiliateCode,
                'status' => 'active', // Instantly active to begin pitching immediately
                'commission_rate' => 15.00, // 15% default commission
                'payout_method' => $preferredMethod,
                'payout_details' => $payoutDetails,
                'notes' => $validated['notes'] ?? 'Signed up via partner onboarding page',
            ]);
        } else {
            // Update payout details if provided
            if (!empty($payoutDetails['upi_id']) || !empty($payoutDetails['account_number'])) {
                $mergedDetails = array_merge($affiliate->payout_details, array_filter($payoutDetails));
                $affiliate->update([
                    'payout_details' => $mergedDetails,
                    'payout_method' => $preferredMethod,
                ]);
            }
        }

        return redirect()->route('affiliate.dashboard')->with('success', '🎉 Welcome to the PSCRanker Partner Program! Your promoter account is active. Start adding your prospective candidates below.');
    }

    /**
     * Affiliate Partner Portal Dashboard (e.g. Anu's mission screen).
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;

        if (!$affiliate) {
            // If user isn't an affiliate yet, redirect to join page
            return redirect()->route('affiliate.join');
        }

        $query = $affiliate->leads()->latest('id');

        // Search in leads
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $cleanPhone = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $cleanPhone) {
                $q->where('candidate_name', 'like', "%{$search}%");
                if (!empty($cleanPhone)) {
                    $q->orWhere('candidate_phone', 'like', "%{$cleanPhone}%");
                }
            });
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->input('status'), ['lead', 'converted'])) {
            $query->where('status', $request->input('status'));
        }

        $leads = $query->paginate(15)->withQueryString();

        // Metrics
        $totalLeads = $affiliate->leads()->count();
        $convertedCount = $affiliate->leads()->where('status', 'converted')->count();
        $totalEarned = $affiliate->totalEarned();
        $pendingPayout = $affiliate->pendingPayout();
        $totalDisbursed = $affiliate->totalDisbursed();

        // Recent Commissions
        $commissions = $affiliate->commissions()->with(['lead', 'student'])->latest('id')->take(10)->get();

        return view('affiliate.dashboard', compact(
            'affiliate',
            'leads',
            'totalLeads',
            'convertedCount',
            'totalEarned',
            'pendingPayout',
            'totalDisbursed',
            'commissions'
        ));
    }

    /**
     * Store a prospective candidate lead (e.g. Anu pitches Santhosh and enters 91234 56789).
     */
    public function storeLead(Request $request)
    {
        $affiliate = Auth::user()->affiliate;

        if (!$affiliate || !$affiliate->isActive()) {
            return redirect()->back()->with('error', 'Your affiliate account is not active.');
        }

        $cleanPhone = AffiliateLead::normalizePhone($request->input('candidate_phone'));
        $request->merge(['candidate_phone' => $cleanPhone]);

        $validated = $request->validate([
            'candidate_name' => ['required', 'string', 'max:150'],
            'candidate_phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'alternate_phone' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'candidate_phone.regex' => 'The mobile number must be exactly 10 digits.',
        ]);

        // Check availability via attribution service
        $availability = $this->attributionService->checkPhoneAvailability($cleanPhone, $affiliate->id);

        if (!$availability['allowed']) {
            return redirect()->back()->withInput()->with('error', $availability['message']);
        }

        // Check if this affiliate already added this number
        $existingSelfLead = $affiliate->leads()
            ->where('candidate_phone', $cleanPhone)
            ->where('status', 'lead')
            ->first();

        if ($existingSelfLead) {
            return redirect()->back()->withInput()->with('error', 'You have already added this candidate to your follow-up list.');
        }

        // Check if student is already a registered user on the platform
        $existingUser = User::where('phone', $cleanPhone)->first();

        $lead = AffiliateLead::create([
            'affiliate_id' => $affiliate->id,
            'candidate_name' => trim($validated['candidate_name']),
            'candidate_phone' => $cleanPhone,
            'alternate_phone' => AffiliateLead::normalizePhone($validated['alternate_phone'] ?? null) ?: null,
            'status' => 'lead',
            'notes' => $validated['notes'] ?? null,
            'converted_user_id' => $existingUser?->id,
            'valid_until' => now()->addDays(60), // 60-day attribution protection window
        ]);

        return redirect()->route('affiliate.dashboard')->with(
            'success',
            "✅ Lead for {$lead->candidate_name} ({$lead->candidate_phone}) saved! When they join the course, your {$affiliate->commission_rate}% commission will be attributed automatically."
        );
    }

    /**
     * Update lead notes or status.
     */
    public function updateLead(Request $request, AffiliateLead $lead)
    {
        $affiliate = Auth::user()->affiliate;

        if (!$affiliate || $lead->affiliate_id !== $affiliate->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'candidate_name' => ['required', 'string', 'max:150'],
        ]);

        $lead->update([
            'candidate_name' => trim($validated['candidate_name']),
            'notes' => $validated['notes'] ?? $lead->notes,
        ]);

        return redirect()->back()->with('success', "Lead details for {$lead->candidate_name} updated successfully.");
    }

    /**
     * Update payout details (UPI ID / Bank Account).
     */
    public function updatePayoutSettings(Request $request)
    {
        $affiliate = Auth::user()->affiliate;

        if (!$affiliate) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payout_method' => ['required', 'in:upi,bank_transfer'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
        ]);

        $details = [
            'upi_id' => $validated['upi_id'] ?? ($affiliate->payout_details['upi_id'] ?? ''),
            'bank_name' => $validated['bank_name'] ?? '',
            'account_holder' => $validated['account_holder'] ?? '',
            'account_number' => $validated['account_number'] ?? '',
            'ifsc_code' => strtoupper($validated['ifsc_code'] ?? ''),
        ];

        $affiliate->update([
            'payout_method' => $validated['payout_method'],
            'payout_details' => $details,
        ]);

        return redirect()->back()->with('success', 'Payout settings updated successfully.');
    }
}
