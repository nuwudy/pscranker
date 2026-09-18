<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLead;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAffiliateController extends Controller
{
    /**
     * Display the Admin Mission Control Affiliate Hub.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'affiliates'); // 'affiliates', 'disbursements', 'leads'

        $defaultCommissionRate = (float) SiteSetting::get('default_affiliate_commission', 15.0);

        // 1. Core Summary Metrics
        $totalAffiliates = Affiliate::count();
        $activeAffiliates = Affiliate::where('status', 'active')->count();
        $totalLeads = AffiliateLead::count();
        $totalConversions = AffiliateLead::where('status', 'converted')->count();
        $lifetimeCommission = (float) AffiliateCommission::sum('total_amount');
        $totalDisbursed = (float) AffiliateCommission::where('status', 'disbursed')->sum('total_amount');
        $totalPending = (float) AffiliateCommission::where('status', 'pending')->sum('total_amount');

        // Month for disbursement ledger (default to previous month e.g. 2026-08 if today is Sep, or selectable)
        $selectedMonth = $request->input('month', now()->subMonth()->format('Y-m'));

        // 2. Affiliates List
        $affiliatesQuery = Affiliate::with(['user'])->withCount(['leads', 'commissions']);
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $cleanPhone = preg_replace('/\D/', '', $search);

            $affiliatesQuery->where(function ($q) use ($search, $cleanPhone) {
                $q->where('affiliate_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $cleanPhone) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                      if (!empty($cleanPhone)) {
                          $uq->orWhere('phone', 'like', "%{$cleanPhone}%");
                      }
                  });
            });
        }
        $affiliates = $affiliatesQuery->latest('id')->paginate(15)->withQueryString();

        // 3. Monthly Disbursement Summaries grouped by Affiliate
        $monthlyDisbursements = AffiliateCommission::where('period_month', $selectedMonth)
            ->select(
                'affiliate_id',
                DB::raw('COUNT(id) as total_sales_count'),
                DB::raw('SUM(course_amount) as total_sales_volume'),
                DB::raw('SUM(commission_amount) as total_commission'),
                DB::raw('SUM(bonus_amount) as total_bonus'),
                DB::raw('SUM(total_amount) as total_payable'),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN total_amount ELSE 0 END) as pending_amount"),
                DB::raw("SUM(CASE WHEN status = 'disbursed' THEN total_amount ELSE 0 END) as disbursed_amount")
            )
            ->groupBy('affiliate_id')
            ->with(['affiliate.user'])
            ->get();

        // 4. Global Leads Pipeline
        $leadsQuery = AffiliateLead::with(['affiliate.user', 'convertedUser']);
        if ($request->filled('lead_q')) {
            $lSearch = trim($request->input('lead_q'));
            $cleanPhone = preg_replace('/\D/', '', $lSearch);

            $leadsQuery->where(function ($q) use ($lSearch, $cleanPhone) {
                $q->where('candidate_name', 'like', "%{$lSearch}%");
                if (!empty($cleanPhone)) {
                    $q->orWhere('candidate_phone', 'like', "%{$cleanPhone}%");
                }
            });
        }
        if ($request->filled('lead_status')) {
            $leadsQuery->where('status', $request->input('lead_status'));
        }
        $leads = $leadsQuery->latest('id')->paginate(20, ['*'], 'leads_page')->withQueryString();

        // Available Months list for dropdown (last 12 months)
        $availableMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $m = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('F Y');
            $availableMonths[$m] = $label;
        }

        // Target vs Payout Slabs (Safe self-healing for production environments)
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('affiliate_slabs')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('affiliate_slabs')) {
                app(\App\Services\AffiliateSlabService::class)->ensureDefaultSlabsExist();
                $slabs = \App\Models\AffiliateSlab::ordered()->get();
            } else {
                $slabs = collect();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AffiliateSlab auto-migration/loading issue: ' . $e->getMessage());
            $slabs = collect();
        }

        return view('admin.affiliates.index', compact(
            'tab',
            'totalAffiliates',
            'activeAffiliates',
            'totalLeads',
            'totalConversions',
            'lifetimeCommission',
            'totalDisbursed',
            'totalPending',
            'affiliates',
            'selectedMonth',
            'monthlyDisbursements',
            'leads',
            'availableMonths',
            'defaultCommissionRate',
            'slabs'
        ));
    }

    /**
     * Update target vs payout slab tiers (Basic Payout % and Bonus % editable).
     */
    public function updateSlabs(Request $request)
    {
        $validated = $request->validate([
            'slabs' => ['required', 'array'],
            'slabs.*.id' => ['required', 'exists:affiliate_slabs,id'],
            'slabs.*.min_target' => ['required', 'numeric', 'min:0'],
            'slabs.*.max_target' => ['nullable', 'numeric'],
            'slabs.*.basic_payout_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'slabs.*.bonus_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'new_slab.min_target' => ['nullable', 'numeric', 'min:0'],
            'new_slab.max_target' => ['nullable', 'numeric'],
            'new_slab.basic_payout_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'new_slab.bonus_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($validated['slabs'] as $slabData) {
            $slab = \App\Models\AffiliateSlab::find($slabData['id']);
            if ($slab) {
                $maxTarget = !empty($slabData['max_target']) ? (float) $slabData['max_target'] : null;
                $slab->update([
                    'min_target' => (float) $slabData['min_target'],
                    'max_target' => $maxTarget,
                    'basic_payout_percentage' => (float) $slabData['basic_payout_percentage'],
                    'bonus_percentage' => (float) $slabData['bonus_percentage'],
                ]);
            }
        }

        // Check if adding a new slab tier
        if (!empty($validated['new_slab']['min_target']) && isset($validated['new_slab']['basic_payout_percentage'])) {
            $newSlab = $validated['new_slab'];
            $maxOrder = \App\Models\AffiliateSlab::max('order') ?? 0;
            $order = $maxOrder + 1;
            $code = \App\Models\AffiliateSlab::generateCode(null, $order);

            \App\Models\AffiliateSlab::create([
                'slab_code' => $code,
                'order' => $order,
                'min_target' => (float) $newSlab['min_target'],
                'max_target' => !empty($newSlab['max_target']) ? (float) $newSlab['max_target'] : null,
                'basic_payout_percentage' => (float) $newSlab['basic_payout_percentage'],
                'bonus_percentage' => (float) ($newSlab['bonus_percentage'] ?? 0.00),
                'is_active' => true,
            ]);
        }

        // Automatically recalculate current month commissions
        $slabService = app(\App\Services\AffiliateSlabService::class);
        $recalc = $slabService->recalculateMonthlyCommissions(now()->format('Y-m'));

        return redirect()->route('admin.affiliates.index', ['tab' => 'slabs'])
            ->with('success', "Target vs Payout slabs saved successfully! Current month payouts updated ({$recalc['affiliates_updated']} promoters synced).");
    }

    /**
     * Recalculate monthly commissions for a specific period.
     */
    public function recalculateCommissions(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $slabService = app(\App\Services\AffiliateSlabService::class);
        $result = $slabService->recalculateMonthlyCommissions($month);

        return redirect()->back()
            ->with('success', "Commissions for {$month} recalculated successfully! Updated {$result['affiliates_updated']} affiliates with total payable of ₹" . number_format($result['total_adjusted_payout'], 2));
    }

    /**
     * Update default platform-wide commission percentage for new affiliates.
     */
    public function updateDefaultCommissionRate(Request $request)
    {
        $validated = $request->validate([
            'default_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        SiteSetting::set('default_affiliate_commission', $validated['default_commission_rate']);

        return redirect()->back()->with('success', "Default affiliate commission rate updated to {$validated['default_commission_rate']}%.");
    }

    /**
     * Update Affiliate status (active, suspended, pending).
     */
    public function updateStatus(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,suspended,pending'],
        ]);

        $affiliate->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', "Affiliate #{$affiliate->affiliate_code} status set to {$validated['status']}.");
    }

    /**
     * Update custom commission rate percentage for a specific affiliate.
     */
    public function updateCommissionRate(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $affiliate->update(['commission_rate' => $validated['commission_rate']]);

        return redirect()->back()->with('success', "Commission rate for {$affiliate->user->name} updated to {$validated['commission_rate']}%.");
    }

    /**
     * Add a discretionary performance bonus to an affiliate.
     */
    public function addBonus(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'bonus_amount' => ['required', 'numeric', 'min:1'],
            'period_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $bonusAmount = (float) $validated['bonus_amount'];

        AffiliateCommission::create([
            'affiliate_id' => $affiliate->id,
            'affiliate_lead_id' => null,
            'subscription_payment_id' => null,
            'user_id' => null,
            'course_amount' => 0,
            'commission_rate' => 0,
            'commission_amount' => 0,
            'bonus_amount' => $bonusAmount,
            'total_amount' => $bonusAmount,
            'period_month' => $validated['period_month'],
            'status' => 'pending',
            'admin_notes' => 'Bonus: ' . $validated['reason'],
        ]);

        return redirect()->back()->with('success', "Performance bonus of ₹{$bonusAmount} credited to {$affiliate->user->name} for {$validated['period_month']}.");
    }

    /**
     * Disburse all pending commissions for an affiliate in a specific month.
     */
    public function disburseMonthly(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'period_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'payout_reference' => ['required', 'string', 'max:100'], // Bank UTR or UPI Transaction ID
            'admin_notes' => ['nullable', 'string', 'max:255'],
        ], [
            'payout_reference.required' => 'Please enter the Bank UTR / UPI Transaction Reference for audit records.',
        ]);

        $periodMonth = $validated['period_month'];
        $ref = trim($validated['payout_reference']);

        $pendingCommissions = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('period_month', $periodMonth)
            ->where('status', 'pending')
            ->get();

        if ($pendingCommissions->isEmpty()) {
            return redirect()->back()->with('info', 'No pending earnings found for this promoter in ' . $periodMonth);
        }

        $totalDisbursed = $pendingCommissions->sum('total_amount');

        AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('period_month', $periodMonth)
            ->where('status', 'pending')
            ->update([
                'status' => 'disbursed',
                'disbursed_at' => now(),
                'payout_reference' => $ref,
                'admin_notes' => $validated['admin_notes'] ?? 'Disbursed via Admin Mission Control',
            ]);

        return redirect()->back()->with(
            'success',
            "🎉 Successfully disbursed ₹" . number_format($totalDisbursed, 2) . " to {$affiliate->user->name} for {$periodMonth}. UTR: {$ref}"
        );
    }

    /**
     * Admin update affiliate's payout details (UPI / Bank Account & IFSC).
     */
    public function updatePayoutDetails(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'upi_id' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
        ]);

        $details = [
            'upi_id' => trim($validated['upi_id'] ?? ''),
            'bank_name' => trim($validated['bank_name'] ?? ''),
            'account_holder' => trim($validated['account_holder'] ?? ''),
            'account_number' => trim($validated['account_number'] ?? ''),
            'ifsc_code' => strtoupper(trim($validated['ifsc_code'] ?? '')),
        ];

        $affiliate->update([
            'payout_method' => !empty($details['account_number']) ? 'bank_transfer' : 'upi',
            'payout_details' => $details,
        ]);

        return redirect()->back()->with('success', "Payout details for {$affiliate->user->name} updated successfully.");
    }
}
